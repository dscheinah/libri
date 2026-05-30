<?php

namespace AppTest\Handler\Account;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Account\AccountListHandler;
use App\Repository\AccountRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class AccountListHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $accounts = [['no' => 1000, 'name' => 'Bank']];
        $repositoryMock = $this->createMock(AccountRepository::class);
        $repositoryMock->expects($this->once())->method('listAccounts')->with(false)->willReturn($accounts);
        
        $handler = new AccountListHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($accounts, $response->data);
    }
}
