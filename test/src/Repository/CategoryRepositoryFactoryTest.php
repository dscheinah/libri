<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\CategoryRepositoryFactory;
use App\Storage\CategoryStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class CategoryRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(CategoryStorage::class, $this->createMock(CategoryStorage::class));
        
        $factory = new CategoryRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
