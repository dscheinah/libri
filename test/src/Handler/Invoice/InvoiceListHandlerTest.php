<?php

namespace AppTest\Handler\Invoice;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Handler\Invoice\InvoiceListHandler;
use App\Repository\InvoiceRepository;
use AppTest\Handler\Mock\Response;
use AppTest\Handler\Mock\ResponseHelper;
use PHPUnit\Framework\TestCase;
use Sx\Message\ServerRequest;

#[AllowMockObjectsWithoutExpectations]
class InvoiceListHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $invoices = [['id' => 1, 'type' => 1]];
        $repositoryMock = $this->createMock(InvoiceRepository::class);
        $repositoryMock->expects($this->once())->method('listInvoices')->with(1, 'Max')->willReturn($invoices);
        
        $handler = new InvoiceListHandler(new ResponseHelper(), $repositoryMock);
        $request = (new ServerRequest())->withQueryParams(['type' => 1, 'search' => 'Max']);
        /** @var Response $response */
        $response = $handler->handle($request);
        
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($invoices, $response->data);
    }
}
