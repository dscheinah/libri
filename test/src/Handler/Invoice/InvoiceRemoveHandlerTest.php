<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceRemoveHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceRemoveHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(InvoiceRepository::class);
        $this->handler = new InvoiceRemoveHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $this->repositoryMock->expects($this->once())->method('removeInvoice')->with(1)->willReturn(true);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 1]));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleFailure(): void
    {
        $this->repositoryMock->expects($this->once())->method('removeInvoice')->willReturn(false);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 1]));
        
        self::assertEquals(500, $response->getStatusCode());
    }
}
