<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceSaveHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceSaveHandlerTest extends TestCase
{
    private $repositoryMock;
    private $handler;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(InvoiceRepository::class);
        $this->handler = new InvoiceSaveHandler(new ResponseHelper(), $this->repositoryMock);
    }

    public function testHandleSuccess(): void
    {
        $data = ['id' => 1, 'date' => '2023-01-01'];
        $this->repositoryMock->expects($this->once())->method('saveInvoice')->with($data, null)->willReturn(true);
        
        /** @var Response $response */
        $response = $this->handler->handle((new ServerRequest())->withParsedBody($data));
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleWithFile(): void
    {
        $data = ['id' => 1];
        $fileMock = $this->createMock(UploadedFileInterface::class);
        $fileMock->method('getSize')->willReturn(100);
        
        $this->repositoryMock->expects($this->once())->method('saveInvoice')->with($data, $fileMock)->willReturn(true);
        
        $request = (new ServerRequest())->withParsedBody($data)->withUploadedFiles(['document' => $fileMock]);
        /** @var Response $response */
        $response = $this->handler->handle($request);
        
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testHandleFailure(): void
    {
        $this->repositoryMock->expects($this->once())->method('saveInvoice')->willReturn(false);
        
        /** @var Response $response */
        $response = $this->handler->handle(new ServerRequest());
        
        self::assertEquals(400, $response->getStatusCode());
    }
}
