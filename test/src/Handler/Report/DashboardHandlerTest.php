<?php

namespace AppTest\Handler\Report;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Report\DashboardHandler;
use App\Repository\DashboardRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class DashboardHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $repositoryMock = $this->createMock(DashboardRepository::class);
        $repositoryMock->expects($this->once())->method('accounts')->willReturn([['a']]);
        $repositoryMock->expects($this->once())->method('categories')->willReturn([['b']]);
        $repositoryMock->expects($this->once())->method('problems')->willReturn([['c']]);
        
        $handler = new DashboardHandler(new ResponseHelper(), $repositoryMock);
        $response = $handler->handle(new ServerRequest());

        $expected = [
            'accounts' => [['a']],
            'categories' => [['b']],
            'problems' => [['c']],
        ];

        self::assertEquals(200, $response->getStatusCode());
        assert($response instanceof Response);
        self::assertEquals($expected, $response->data);
    }
}
