<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\InvoiceRepositoryFactory;
use App\Storage\AssignmentStorage;
use App\Storage\InvoiceStorage;
use App\Storage\MasterStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class InvoiceRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(InvoiceStorage::class, $this->createMock(InvoiceStorage::class));
        $injector->set(AssignmentStorage::class, $this->createMock(AssignmentStorage::class));
        $injector->set(MasterStorage::class, $this->createMock(MasterStorage::class));
        
        $factory = new InvoiceRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
