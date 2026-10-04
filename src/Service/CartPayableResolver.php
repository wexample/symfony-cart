<?php

namespace Wexample\SymfonyCart\Service;

use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyCart\Entity\Cart;
use Wexample\SymfonyCart\Repository\CartRepository;
use Wexample\SymfonyPayment\Interface\PayableInterface;
use Wexample\SymfonyPayment\Interface\PayableResolverInterface;

class CartPayableResolver implements PayableResolverInterface
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    ) {
    }

    public function supports(string $payableType): bool
    {
        return Cart::PAYABLE_TYPE === $payableType;
    }

    public function resolve(
        string $payableType,
        string $payableId
    ): ?PayableInterface {
        return Uuid::isValid($payableId) ? $this->cartRepository->find(Uuid::fromString($payableId)) : null;
    }
}
