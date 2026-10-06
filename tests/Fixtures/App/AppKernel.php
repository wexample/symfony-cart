<?php

namespace Wexample\SymfonyCart\Tests\Fixtures\App;

use Wexample\SymfonyCart\WexampleSymfonyCartBundle;
use Wexample\SymfonyGeo\WexampleSymfonyGeoBundle;
use Wexample\SymfonyMoney\WexampleSymfonyMoneyBundle;
use Wexample\SymfonyPayment\WexampleSymfonyPaymentBundle;
use Wexample\SymfonyRemotePayment\WexampleSymfonyRemotePaymentBundle;
use Wexample\SymfonyTesting\Tests\Fixtures\AbstractFixtureKernel;

class AppKernel extends AbstractFixtureKernel
{
    protected function getFixtureDir(): string
    {
        return __DIR__;
    }

    protected function getExtraBundles(): iterable
    {
        return [
            new WexampleSymfonyMoneyBundle(),
            new WexampleSymfonyGeoBundle(),
            new WexampleSymfonyRemotePaymentBundle(),
            new WexampleSymfonyPaymentBundle(),
            new WexampleSymfonyCartBundle(),
        ];
    }

    protected function getConfigFiles(): array
    {
        return [
            __DIR__.'/config/config.yaml',
        ];
    }
}
