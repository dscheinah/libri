<?php

namespace AppTest\Handler\Contact;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Contact\ContactListHandler;
use App\Repository\ContactRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ContactListHandlerTest extends TestCase
{
    public function testHandleWithoutSearch(): void
    {
        $contacts = [['id' => 1, 'name' => 'Max']];
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('listContacts')->with('')->willReturn($contacts);
        
        $handler = new ContactListHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($contacts, $response->data);
    }

    public function testHandleWithSearch(): void
    {
        $contacts = [['id' => 1, 'name' => 'Max']];
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('listContacts')->with('Max')->willReturn($contacts);
        
        $handler = new ContactListHandler(new ResponseHelper(), $repositoryMock);
        $request = (new ServerRequest())->withQueryParams(['search' => 'Max']);
        /** @var Response $response */
        $response = $handler->handle($request);
        
        self::assertEquals(200, $response->getStatusCode());
    }
}
