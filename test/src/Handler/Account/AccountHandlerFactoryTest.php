<?php

namespace AppTest\Handler\Account;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Account\AccountHandlerFactory;
use App\Handler\Account\AccountListHandler;
use App\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class AccountHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(AccountRepository::class, $this->createMock(AccountRepository::class));
        
        $factory = new AccountHandlerFactory();
        $handler = $factory->create($injector, [], AccountListHandler::class);
        
        self::assertInstanceOf(AccountListHandler::class, $handler);
    }
}
