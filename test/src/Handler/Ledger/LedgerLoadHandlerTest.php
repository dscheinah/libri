<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerLoadHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerLoadHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(LedgerRepository::class);
        $this->handler = new LedgerLoadHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $ledger = ['id' => 1];
        $this->repositoryMock->expects($this->once())->method('getLedger')->with(1)->willReturn($ledger);
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 1]));
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($ledger, $response->data);
    }

    public function testHandleNotFound(): void
    {
        $this->repositoryMock->expects($this->once())->method('getLedger')->willReturn(null);
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withQueryParams(['id' => 999]));
        self::assertEquals(404, $response->getStatusCode());
    }
}
