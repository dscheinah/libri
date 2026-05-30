<?php

namespace AppTest\Handler\Contact;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Contact\ContactRemoveHandler;
use App\Repository\ContactRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ContactRemoveHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('removeContact')->with(123);
        
        $handler = new ContactRemoveHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withQueryParams(['id' => 123]));
        
        self::assertEquals(204, $response->getStatusCode());
    }
}
