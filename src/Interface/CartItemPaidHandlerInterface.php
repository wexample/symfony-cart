<?php

namespace Wexample\SymfonyCart\Interface;

use Wexample\SymfonyCart\Entity\CartItem;

/**
 * What happens when an item is paid, chosen by product type: renew a membership,
 * issue a ticket, thank a donor. Called once per paid item.
 */
interface CartItemPaidHandlerInterface
{
    public function supports(string $productType): bool;

    public function handle(CartItem $item): void;
}
