<?php

namespace AppTest\Handler\Contact;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Contact\ContactLoadHandler;
use App\Repository\ContactRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ContactLoadHandlerTest extends TestCase
{
    public function testHandleFound(): void
    {
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('getContact')->with(1)->willReturn(['id' => 1, 'name' => 'Max']);
        
        $handler = new ContactLoadHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withQueryParams(['id' => 1]));
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['id' => 1, 'name' => 'Max'], $response->data);
    }

    public function testHandleNotFound(): void
    {
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('getContact')->with(999)->willReturn(null);
        
        $handler = new ContactLoadHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withQueryParams(['id' => 999]));
        
        self::assertEquals(404, $response->getStatusCode());
    }
}
