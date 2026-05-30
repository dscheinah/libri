<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerHandlerFactory;
use App\Handler\Ledger\LedgerListHandler;
use App\Repository\LedgerRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class LedgerHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(LedgerRepository::class, $this->createMock(LedgerRepository::class));
        
        $factory = new LedgerHandlerFactory();
        $handler = $factory->create($injector, [], LedgerListHandler::class);
        
        self::assertInstanceOf(LedgerListHandler::class, $handler);
    }
}
