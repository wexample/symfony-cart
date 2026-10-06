<?php

namespace Wexample\SymfonyCart\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyGeo\Entity\AbstractAddress;

/**
 * The billing or shipping address given at checkout.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cart_address')]
class CartAddress extends AbstractAddress
{
}
