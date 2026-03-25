<?php

namespace App\Repository;

use App\Storage\CategoryStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class CategoryRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): CategoryRepository
    {
        $storage = $injector->get(CategoryStorage::class);
        assert($storage instanceof CategoryStorage);
        return new CategoryRepository(
            $storage,
        );
    }
}
