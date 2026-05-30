<?php

namespace AppTest\Handler\Report;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Report\ReportCancellationHandler;
use App\Repository\ReportRepository;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ReportCancellationHandlerTest extends TestCase
{
    public function testHandleMissingData(): void
    {
        $repositoryMock = $this->createMock(ReportRepository::class);
        $handler = new ReportCancellationHandler($repositoryMock);
        
        $response = $handler->handle(new ServerRequest());
        self::assertEquals(400, $response->getStatusCode());
    }
}
