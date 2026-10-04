<?php

namespace Wexample\SymfonyCart\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyCart\Interface\CartItemPaidHandlerInterface;
use Wexample\SymfonyCart\Interface\PriceResolverInterface;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;

class WexampleSymfonyCartExtension extends AbstractWexampleSymfonyExtension
{
    public const string TAG_PAID_HANDLER = 'wexample_symfony_cart.paid_handler';

    public const string TAG_PRICE_RESOLVER = 'wexample_symfony_cart.price_resolver';

    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $container
            ->registerForAutoconfiguration(CartItemPaidHandlerInterface::class)
            ->addTag(self::TAG_PAID_HANDLER);

        $container
            ->registerForAutoconfiguration(PriceResolverInterface::class)
            ->addTag(self::TAG_PRICE_RESOLVER);

        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}
