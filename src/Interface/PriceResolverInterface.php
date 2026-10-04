<?php

namespace Wexample\SymfonyCart\Interface;

use Wexample\SymfonyCart\Entity\Cart;
use Wexample\SymfonyCart\Entity\Product;

/**
 * Chooses the unit price (before VAT) of a product for a cart: member prices,
 * customer groups. Return null to leave the decision to the next resolver;
 * the product price applies when every resolver abstains.
 */
interface PriceResolverInterface
{
    public function resolvePrice(
        Product $product,
        Cart $cart
    ): ?int;
}
