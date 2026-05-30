<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerRemoveHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerRemoveHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(LedgerRepository::class);
        $this->handler = new LedgerRemoveHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandle(): void
    {
        $data = ['id' => 1, 'reason' => 'Duplicate'];
        $this->repositoryMock->expects($this->once())->method('cancelLedger')->with(1, 'Duplicate');
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingData(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest()));
        self::assertEquals(400, $response->getStatusCode());
    }
}
