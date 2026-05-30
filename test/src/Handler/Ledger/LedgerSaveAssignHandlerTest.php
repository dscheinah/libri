<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerSaveAssignHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerSaveAssignHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(LedgerRepository::class);
        $this->handler = new LedgerSaveAssignHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $data = ['id' => 1, 'invoices' => [100, 200]];
        $this->repositoryMock->expects($this->once())->method('assignInvoices')->with(1, [100, 200]);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingId(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['invoices' => [100]]));
        self::assertEquals(400, $response->getStatusCode());
    }

    public function testHandleMissingInvoices(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['id' => 1]));
        self::assertEquals(400, $response->getStatusCode());
    }
}
