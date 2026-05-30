<?php

namespace AppTest\Handler\Category;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Category\CategorySaveHandler;
use App\Repository\CategoryRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class CategorySaveHandlerTest extends TestCase
{
    public function testHandleSuccess(): void
    {
        $names = ['Office', 'Travel'];
        $repositoryMock = $this->createMock(CategoryRepository::class);
        $repositoryMock->expects($this->once())->method('updateCategories')->with($names);
        
        $handler = new CategorySaveHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withParsedBody(['name' => $names]));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleMissingData(): void
    {
        $repositoryMock = $this->createMock(CategoryRepository::class);
        $handler = new CategorySaveHandler(new ResponseHelper(), $repositoryMock);
        
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(400, $response->getStatusCode());
    }
}
