<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\LedgerRepositoryFactory;
use App\Storage\AccountStorage;
use App\Storage\AssignmentStorage;
use App\Storage\LedgerStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class LedgerRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(LedgerStorage::class, $this->createMock(LedgerStorage::class));
        $injector->set(AssignmentStorage::class, $this->createMock(AssignmentStorage::class));
        $injector->set(AccountStorage::class, $this->createMock(AccountStorage::class));
        
        $factory = new LedgerRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
