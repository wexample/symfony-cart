<?php

namespace Wexample\SymfonyCart\Tests\Fixtures\Handler;

use Wexample\SymfonyCart\Entity\CartItem;
use Wexample\SymfonyCart\Interface\CartItemPaidHandlerInterface;

class RecordingPaidHandler implements CartItemPaidHandlerInterface
{
    /** @var list<CartItem> */
    public array $handled = [];

    public function supports(string $productType): bool
    {
        return 'membership' === $productType;
    }

    public function handle(CartItem $item): void
    {
        $this->handled[] = $item;
    }
}
