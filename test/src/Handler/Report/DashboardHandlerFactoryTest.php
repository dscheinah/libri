<?php

namespace AppTest\Handler\Report;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Report\DashboardHandler;
use App\Handler\Report\DashboardHandlerFactory;
use App\Repository\DashboardRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class DashboardHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(DashboardRepository::class, $this->createMock(DashboardRepository::class));
        
        $factory = new DashboardHandlerFactory();
        $handler = $factory->create($injector, [], '');
        
        self::assertInstanceOf(DashboardHandler::class, $handler);
    }
}
