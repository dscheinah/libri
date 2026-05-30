<?php

namespace AppTest\Handler\Category;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Category\CategoryListHandler;
use App\Repository\CategoryRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class CategoryListHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $categories = ['Office', 'Travel'];
        $repositoryMock = $this->createMock(CategoryRepository::class);
        $repositoryMock->expects($this->once())->method('listCategories')->willReturn($categories);
        
        $handler = new CategoryListHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($categories, $response->data);
    }
}
