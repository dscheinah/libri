<?php

namespace AppTest\Handler\Master;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Master\MasterSaveHandler;
use App\Repository\MasterRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class MasterSaveHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $data = ['vat' => '19'];
        $repositoryMock = $this->createMock(MasterRepository::class);
        $repositoryMock->expects($this->once())->method('storeEntries')->with($data);
        
        $handler = new MasterSaveHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }
}
