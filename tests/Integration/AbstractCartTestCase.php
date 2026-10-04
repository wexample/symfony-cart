<?php

namespace Wexample\SymfonyCart\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wexample\SymfonyCart\Entity\Product;
use Wexample\SymfonyCart\Service\CartCheckoutService;
use Wexample\SymfonyCart\Service\CartService;
use Wexample\SymfonyCart\Service\StockService;
use Wexample\SymfonyPayment\Service\PaymentService;
use Wexample\SymfonyRemotePayment\Class\FakePaymentProvider;

abstract class AbstractCartTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $entityManager = $this->em();
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function em(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function carts(): CartService
    {
        return static::getContainer()->get(CartService::class);
    }

    protected function checkout(): CartCheckoutService
    {
        return static::getContainer()->get(CartCheckoutService::class);
    }

    protected function stock(): StockService
    {
        return static::getContainer()->get(StockService::class);
    }

    protected function payments(): PaymentService
    {
        return static::getContainer()->get(PaymentService::class);
    }

    protected function provider(): FakePaymentProvider
    {
        return static::getContainer()->get(FakePaymentProvider::class);
    }

    protected function product(
        int $priceRaw = 25000,
        int $vat = 2000,
        string $type = 'default',
        string $slug = 'product'
    ): Product {
        $product = (new Product())
            ->setTitle(ucfirst($slug))
            ->setSlug($slug)
            ->setType($type)
            ->setPriceRaw($priceRaw)
            ->setPriceVat($vat);

        $this->em()->persist($product);
        $this->em()->flush();

        return $product;
    }
}
