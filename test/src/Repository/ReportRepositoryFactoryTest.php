<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\ReportRepositoryFactory;
use App\Storage\AccountStorage;
use App\Storage\CategoryStorage;
use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class ReportRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(AccountStorage::class, $this->createMock(AccountStorage::class));
        $injector->set(CategoryStorage::class, $this->createMock(CategoryStorage::class));
        $injector->set(InvoiceStorage::class, $this->createMock(InvoiceStorage::class));
        $injector->set(LedgerStorage::class, $this->createMock(LedgerStorage::class));
        
        $factory = new ReportRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
