<?php

namespace Wexample\SymfonyCart\Tests\Fixtures\Handler;

use Wexample\SymfonyCart\Entity\Cart;
use Wexample\SymfonyCart\Entity\Product;
use Wexample\SymfonyCart\Interface\PriceResolverInterface;

/**
 * Members (owner "member-*") pay half price.
 */
class MemberPriceResolver implements PriceResolverInterface
{
    public function resolvePrice(Product $product, Cart $cart): ?int
    {
        return str_starts_with((string) $cart->getOwnerReference(), 'member-')
            ? intdiv((int) $product->getPriceRaw(), 2)
            : null;
    }
}
