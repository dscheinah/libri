<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\MasterRepositoryFactory;
use App\Storage\MasterStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class MasterRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(MasterStorage::class, $this->createMock(MasterStorage::class));
        
        $factory = new MasterRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
