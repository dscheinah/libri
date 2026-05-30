<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\DashboardRepositoryFactory;
use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class DashboardRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(InvoiceStorage::class, $this->createMock(InvoiceStorage::class));
        $injector->set(LedgerStorage::class, $this->createMock(LedgerStorage::class));
        
        $factory = new DashboardRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
