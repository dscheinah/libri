<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceLoadHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceLoadHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(InvoiceRepository::class);
        $this->handler = new InvoiceLoadHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $invoice = ['id' => 1];
        $this->repositoryMock->expects($this->once())->method('getInvoice')->with(1)->willReturn($invoice);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 1]));
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($invoice, $response->data);
    }

    public function testHandleNotFound(): void
    {
        $this->repositoryMock->expects($this->once())->method('getInvoice')->willReturn(null);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 999]));
        
        self::assertEquals(404, $response->getStatusCode());
    }
}
