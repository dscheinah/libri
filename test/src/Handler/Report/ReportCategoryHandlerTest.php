<?php

namespace AppTest\Handler\Report;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Report\ReportCategoryHandler;
use App\Repository\ReportRepository;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ReportCategoryHandlerTest extends TestCase
{
    public function testHandleMissingData(): void
    {
        $repositoryMock = $this->createMock(ReportRepository::class);
        $handler = new ReportCategoryHandler($repositoryMock);
        
        $response = $handler->handle(new ServerRequest());
        self::assertEquals(400, $response->getStatusCode());
    }
}
