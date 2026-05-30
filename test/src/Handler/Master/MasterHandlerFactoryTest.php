<?php

namespace AppTest\Handler\Master;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Master\MasterHandlerFactory;
use App\Handler\Master\MasterLoadHandler;
use App\Repository\MasterRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class MasterHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(MasterRepository::class, $this->createMock(MasterRepository::class));
        
        $factory = new MasterHandlerFactory();
        $handler = $factory->create($injector, [], MasterLoadHandler::class);
        
        self::assertInstanceOf(MasterLoadHandler::class, $handler);
    }
}
