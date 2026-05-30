<?php

namespace AppTest\Handler\Master;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Master\MasterLoadHandler;
use App\Repository\MasterRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class MasterLoadHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $entries = [['key' => 'vat', 'value' => '19']];
        $repositoryMock = $this->createMock(MasterRepository::class);
        $repositoryMock->expects($this->once())->method('loadEntries')->willReturn($entries);
        
        $handler = new MasterLoadHandler(new ResponseHelper(), $repositoryMock);
        /** @var Response $response */
        $response = $handler->handle(new ServerRequest());
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($entries, $response->data);
    }
}
