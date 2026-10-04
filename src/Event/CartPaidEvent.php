<?php

namespace Wexample\SymfonyCart\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Wexample\SymfonyCart\Entity\Cart;

/**
 * Dispatched once, when a cart's payment succeeded. Invoices, mails and
 * memberships are listeners of this event, not package code.
 */
class CartPaidEvent extends Event
{
    public function __construct(public readonly Cart $cart)
    {
    }
}
