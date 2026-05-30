<?php

namespace AppTest\Handler\Contact;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Contact\ContactHandlerFactory;
use App\Handler\Contact\ContactListHandler;
use App\Repository\ContactRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class ContactHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(ContactRepository::class, $this->createMock(ContactRepository::class));
        
        $factory = new ContactHandlerFactory();
        $handler = $factory->create($injector, [], ContactListHandler::class);
        
        self::assertInstanceOf(ContactListHandler::class, $handler);
    }
}
