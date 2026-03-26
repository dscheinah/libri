<?php

namespace App\Repository;

use App\Storage\ContactStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ContactRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): ContactRepository
    {
        $storage = $injector->get(ContactStorage::class);
        assert($storage instanceof ContactStorage);
        return new ContactRepository(
            $storage,
        );
    }
}
