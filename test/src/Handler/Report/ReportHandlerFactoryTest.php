<?php

namespace AppTest\Handler\Report;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Report\ReportAccountHandler;
use App\Handler\Report\ReportHandlerFactory;
use App\Repository\ReportRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;

#[AllowMockObjectsWithoutExpectations]
class ReportHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ReportRepository::class, $this->createMock(ReportRepository::class));
        
        $factory = new ReportHandlerFactory();
        $handler = $factory->create($injector, [], ReportAccountHandler::class);
        
        self::assertInstanceOf(ReportAccountHandler::class, $handler);
    }
}
