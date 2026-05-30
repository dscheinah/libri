<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceSaveAssignHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceSaveAssignHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(InvoiceRepository::class);
        $this->handler = new InvoiceSaveAssignHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $data = ['id' => 1, 'ledgers' => [10, 20]];
        $this->repositoryMock->expects($this->once())->method('assignLedgers')->with(1, [10, 20]);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingId(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['ledgers' => [10]]));
        self::assertEquals(400, $response->getStatusCode());
    }

    public function testHandleMissingLedgers(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['id' => 1]));
        self::assertEquals(400, $response->getStatusCode());
    }
}
