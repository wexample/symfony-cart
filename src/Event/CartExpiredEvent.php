<?php

namespace Wexample\SymfonyCart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Wexample\SymfonyCart\Entity\Cart;

/**
 * Dispatched when an abandoned cart expires, e.g. to delete a disabled shadow user.
 */
class CartExpiredEvent extends Event
{
    public function __construct(public readonly Cart $cart)
    {
    }
}
