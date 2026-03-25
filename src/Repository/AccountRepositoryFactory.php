<?php

namespace App\Repository;

use App\Storage\AccountStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class AccountRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): AccountRepository
    {
        $storage = $injector->get(AccountStorage::class);
        assert($storage instanceof AccountStorage);
        return new AccountRepository(
            $storage,
        );
    }
}
