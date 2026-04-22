<?php

namespace App\Repository;

use App\Storage\AssignmentStorage;
use App\Storage\InvoiceStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class InvoiceRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): InvoiceRepository
    {
        $storage = $injector->get(InvoiceStorage::class);
        assert($storage instanceof InvoiceStorage);
        $assignmentStorage = $injector->get(AssignmentStorage::class);
        assert($assignmentStorage instanceof AssignmentStorage);
        return new InvoiceRepository(
            $storage,
            $assignmentStorage,
        );
    }
}
