<?php

namespace AppTest\Handler\Contact;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Contact\ContactSaveHandler;
use App\Repository\ContactRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class ContactSaveHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $data = ['name' => 'Max'];
        $repositoryMock = $this->createMock(ContactRepository::class);
        $repositoryMock->expects($this->once())->method('saveContact')->with($data);
        
        $handler = new ContactSaveHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }
}
