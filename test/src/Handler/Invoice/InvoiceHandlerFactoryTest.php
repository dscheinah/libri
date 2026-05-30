<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceHandlerFactory;
use App\Handler\Invoice\InvoiceListHandler;
use App\Repository\InvoiceRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class InvoiceHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(InvoiceRepository::class, $this->createMock(InvoiceRepository::class));
        
        $factory = new InvoiceHandlerFactory();
        $handler = $factory->create($injector, [], InvoiceListHandler::class);
        
        self::assertInstanceOf(InvoiceListHandler::class, $handler);
    }
}
