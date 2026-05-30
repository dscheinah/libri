<?php

namespace AppTest\Repository;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\ContactRepositoryFactory;
use App\Storage\ContactStorage;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class ContactRepositoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ContactStorage::class, $this->createMock(ContactStorage::class));
        
        $factory = new ContactRepositoryFactory();
        $repository = $factory->create($injector, [], '');
        
        self::assertNotNull($repository);
    }
}
