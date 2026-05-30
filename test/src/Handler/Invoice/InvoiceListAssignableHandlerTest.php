<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceListAssignableHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceListAssignableHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $invoices = [['id' => 1]];
        $repositoryMock = $this->createMock(InvoiceRepository::class);
        $repositoryMock->expects($this->once())->method('listOpenInvoices')->willReturn($invoices);
        
        $handler = new InvoiceListAssignableHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($invoices, $response->data);
    }
}
