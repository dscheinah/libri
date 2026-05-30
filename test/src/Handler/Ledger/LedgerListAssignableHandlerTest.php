<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerListAssignableHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerListAssignableHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $ledgers = [['id' => 1]];
        $repositoryMock = $this->createMock(LedgerRepository::class);
        $repositoryMock->expects($this->once())->method('listOpenLedgers')->willReturn($ledgers);
        
        $handler = new LedgerListAssignableHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($ledgers, $response->data);
    }
}
