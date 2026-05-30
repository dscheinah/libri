<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerListHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerListHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $ledgers = [['id' => 1]];
        $repositoryMock = $this->createMock(LedgerRepository::class);
        $repositoryMock->expects($this->once())->method('listLedgers')->with('1000', 'Max')->willReturn($ledgers);
        
        $handler = new LedgerListHandler(new ResponseHelper(), $repositoryMock);
        $request = (new ServerRequest())->withQueryParams(['account' => '1000', 'search' => 'Max']);
        /** @var Response $response */
        $response = $handler->handle($request);
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($ledgers, $response->data);
    }
}
