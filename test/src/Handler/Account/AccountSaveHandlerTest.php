<?php

namespace AppTest\Handler\Account;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Account\AccountSaveHandler;
use App\Repository\AccountRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class AccountSaveHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(AccountRepository::class);
        $this->handler = new AccountSaveHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $data = [
            'no' => [1000],
            'name' => ['Bank'],
            'category' => ['Asset'],
            'real' => [1000 => 'on']
        ];
        
        $this->repositoryMock->expects($this->once())->method('updateAccounts')
            ->with([1000], ['Bank'], ['Asset'], [1000 => 'on']);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingData(): void
    {
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody(['no' => [1000]]));
        self::assertEquals(400, $response->getStatusCode());
    }

    public function testHandleMismatchedCount(): void
    {
        $data = [
            'no' => [1000, 1100],
            'name' => ['Bank'],
            'category' => ['Asset'],
            'real' => []
        ];
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        self::assertEquals(400, $response->getStatusCode());
    }
}
