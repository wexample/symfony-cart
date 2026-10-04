<?php

namespace Wexample\SymfonyCart\Repository;

use Wexample\SymfonyCart\Entity\Product;
use Wexample\SymfonyCart\Enum\ProductStatus;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 */
class ProductRepository extends AbstractRepository
{
    public static function getEntityClassName(): string
    {
        return Product::class;
    }

    public function findOneBySlug(string $slug): ?Product
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * @return Product[]
     */
    public function findSalable(?string $type = null): array
    {
        return $this->findBy(array_filter(['status' => ProductStatus::Active, 'type' => $type]), ['title' => self::SORT_ASC]);
    }
}
