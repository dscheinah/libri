<?php

namespace AppTest\Handler\Category;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Category\CategoryHandlerFactory;
use App\Handler\Category\CategoryListHandler;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

#[AllowMockObjectsWithoutExpectations]
class CategoryHandlerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $injector = new Injector();
        $injector->set(ResponseHelperInterface::class, $this->createMock(ResponseHelperInterface::class));
        $injector->set(CategoryRepository::class, $this->createMock(CategoryRepository::class));
        
        $factory = new CategoryHandlerFactory();
        $handler = $factory->create($injector, [], CategoryListHandler::class);
        
        self::assertInstanceOf(CategoryListHandler::class, $handler);
    }
}
