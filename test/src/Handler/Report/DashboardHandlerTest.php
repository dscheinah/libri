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
        $repositoryMock->expects($this->once())->method('accounts')->willReturn(0.0);
        $repositoryMock->expects($this->once())->method('categories')->willReturn([]);
        $repositoryMock->expects($this->once())->method('problems')->willReturn([]);
        
        $handler = new DashboardHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        $data = $response->data;
        self::assertArrayHasKey('accounts', $data);
        self::assertArrayHasKey('categories', $data);
        self::assertArrayHasKey('problems', $data);
    }
}
