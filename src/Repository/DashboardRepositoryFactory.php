<?php

namespace App\Repository;

use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class DashboardRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): DashboardRepository
    {
        $invoiceStorage = $injector->get(InvoiceStorage::class);
        assert($invoiceStorage instanceof InvoiceStorage);
        $ledgerStorage = $injector->get(LedgerStorage::class);
        assert($ledgerStorage instanceof LedgerStorage);
        return new DashboardRepository(
            $invoiceStorage,
            $ledgerStorage,
        );
    }
}
