<?php

namespace Wexample\SymfonyCart\Service;

use Doctrine\ORM\EntityManagerInterface;
use Wexample\SymfonyCart\Entity\Cart;
use Wexample\SymfonyCart\Entity\CartItem;
use Wexample\SymfonyCart\Entity\Product;
use Wexample\SymfonyCart\Entity\ProductVariation;
use Wexample\SymfonyCart\Entity\Schedule;
use Wexample\SymfonyCart\Interface\PriceResolverInterface;
use Wexample\SymfonyCart\Repository\CartRepository;

/**
 * Filling a cart. Every change refuses a cart that is no longer opened.
 */
class CartService
{
    /**
     * @param iterable<PriceResolverInterface> $priceResolvers
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CartRepository $cartRepository,
        private readonly iterable $priceResolvers = [],
    ) {
    }

    public function create(
        ?string $ownerReference = null,
        string $currencyCode = 'EUR'
    ): Cart {
        $cart = (new Cart())
            ->setOwnerReference($ownerReference)
            ->setCurrencyCode($currencyCode);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    /**
     * The owner's opened cart, or a new one.
     */
    public function getOrCreateOpened(string $ownerReference): Cart
    {
        return $this->cartRepository->findOpenedByOwner($ownerReference) ?? $this->create($ownerReference);
    }

    /**
     * Gives an anonymous cart to its buyer once known.
     */
    public function attachOwner(
        Cart $cart,
        string $ownerReference,
        ?string $customerEmail = null
    ): Cart {
        $cart->setOwnerReference($ownerReference);

        if ($customerEmail) {
            $cart->setCustomerEmail($customerEmail);
        }

        $this->entityManager->flush();

        return $cart;
    }

    /**
     * @param ProductVariation[] $variations
     * @param int|null $amount For a free-amount product, the amount chosen, before VAT.
     */
    public function addProduct(
        Cart $cart,
        Product $product,
        int $quantity = 1,
        ?int $amount = null,
        ?Schedule $schedule = null,
        array $variations = [],
        ?string $comment = null,
    ): CartItem {
        $cart->assertEditable();

        if (! $product->isSalable()) {
            throw new \InvalidArgumentException(sprintf('Product "%s" is not for sale.', $product->getSlug()));
        }

        if (null !== $amount && ! $product->isFreeAmount()) {
            throw new \InvalidArgumentException(sprintf('Product "%s" has a fixed price.', $product->getSlug()));
        }

        $item = new CartItem();
        $item->setProduct($product, $amount ?? $this->resolvePrice($product, $cart))
            ->setSchedule($schedule)
            ->setComment($comment);

        foreach ($variations as $variation) {
            if ($variation->getProduct() !== $product) {
                throw new \InvalidArgumentException('A variation of another product cannot be chosen.');
            }

            $item->addVariation($variation);
        }

        $item->setQuantity($quantity);
        $cart->addItem($item);

        if (null !== $product->getMaxPerCart()) {
            $this->limitProductItems($cart, $product, $schedule, $product->getMaxPerCart(), $item);
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * Keeps at most $max items of the product (for the same schedule, or without one),
     * the most recent first. The kept item is $keep when given.
     */
    public function limitProductItems(
        Cart $cart,
        Product $product,
        ?Schedule $schedule = null,
        int $max = 1,
        ?CartItem $keep = null,
    ): void {
        $cart->assertEditable();
        $matching = [];

        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() === $product && $item->getSchedule() === $schedule) {
                $matching[] = $item;
            }
        }

        // Most recent last; the item to keep goes last whatever its position.
        if ($keep) {
            $matching = array_values(array_filter($matching, fn (CartItem $item) => $item !== $keep));
            $matching[] = $keep;
        }

        foreach (array_slice($matching, 0, max(0, count($matching) - $max)) as $item) {
            $cart->removeItem($item);
        }

        $this->entityManager->flush();
    }

    public function setQuantity(
        CartItem $item,
        int $quantity
    ): void {
        $item->getCart()->assertEditable();

        if ($quantity <= 0) {
            $this->removeItem($item);

            return;
        }

        $item->setQuantity($quantity);
        $this->entityManager->flush();
    }

    public function removeItem(CartItem $item): void
    {
        $item->getCart()->removeItem($item);
        $this->entityManager->flush();
    }

    public function removeProduct(
        Cart $cart,
        Product $product
    ): void {
        foreach ($cart->getItems()->toArray() as $item) {
            if ($item->getProduct() === $product) {
                $cart->removeItem($item);
            }
        }

        $this->entityManager->flush();
    }

    public function clear(Cart $cart): void
    {
        foreach ($cart->getItems()->toArray() as $item) {
            $cart->removeItem($item);
        }

        $this->entityManager->flush();
    }

    /**
     * Refreshes stored totals, e.g. after editing a variation.
     */
    public function recompute(Cart $cart): void
    {
        foreach ($cart->getItems() as $item) {
            $item->applyVariations();
        }

        $cart->updatePriceTotal();
        $this->entityManager->flush();
    }

    private function resolvePrice(
        Product $product,
        Cart $cart
    ): ?int {
        foreach ($this->priceResolvers as $resolver) {
            $price = $resolver->resolvePrice($product, $cart);

            if (null !== $price) {
                return $price;
            }
        }

        return $product->getPriceRaw();
    }
}
