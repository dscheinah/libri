<?php

namespace App\Repository;

use App\Storage\AccountStorage;
use App\Storage\CategoryStorage;
use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

/**
 * Factory for the ReportRepository.
 */
class ReportRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): ReportRepository
    {
        $accountStorage = $injector->get(AccountStorage::class);
        assert($accountStorage instanceof AccountStorage);
        $categoryStorage = $injector->get(CategoryStorage::class);
        assert($categoryStorage instanceof CategoryStorage);
        $invoiceStorage = $injector->get(InvoiceStorage::class);
        assert($invoiceStorage instanceof InvoiceStorage);
        $ledgerStorage = $injector->get(LedgerStorage::class);
        assert($ledgerStorage instanceof LedgerStorage);
        return new ReportRepository(
            $accountStorage,
            $categoryStorage,
            $invoiceStorage,
            $ledgerStorage,
        );
    }
}
