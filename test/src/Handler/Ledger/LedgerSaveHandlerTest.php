<?php

namespace AppTest\Handler\Ledger;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Ledger\LedgerSaveHandler;
use App\Repository\LedgerRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class LedgerSaveHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(LedgerRepository::class);
        $this->handler = new LedgerSaveHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $data = [
            'date' => ['2023-01-01'],
            'account' => ['1000'],
            'offset' => ['8000'],
            'amount' => [100.0],
            'description' => ['Test'],
            'reference' => ['Ref']
        ];
        
        $this->repositoryMock->expects($this->once())->method('createLedgers')
            ->with(1, ['2023-01-01'], ['1000'], ['8000'], [100.0], ['Test'], ['Ref']);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingData(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['date' => []]));
        self::assertEquals(400, $response->getStatusCode());
    }

    public function testHandleMismatchedCount(): void
    {
        $data = [
            'date' => ['2023-01-01', '2023-01-02'],
            'account' => ['1000'],
            'offset' => ['8000'],
            'amount' => [100.0],
            'description' => ['Test'],
            'reference' => ['Ref']
        ];
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        self::assertEquals(400, $response->getStatusCode());
    }
}
