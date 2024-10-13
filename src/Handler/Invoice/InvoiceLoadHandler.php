<?php

namespace App\Handler\Invoice;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $invoices = [
            [
                "id" => 1,
                "type" => 1,
                "date" => "2021-01-01",
                "description" => "erat Vivamus nisi Mauris",
                "amount" => 240.86,
                "assigned" => true,
                "ledgers" => [['id' => 1, 'description' => 'non lacinia at iaculis quis']],
                "reference" => "16611026 1549",
                "document" => [
                    "link" => "#",
                    "name" => "R2L 8E4.pdf",
                ],
                "no_document" => true,
                "contact" => null,
                "finished" => false,
            ],
            [
                "id" => 2,
                "type" => 1,
                "date" => "2021-01-11",
                "description" => "sem Pellentesque ut ipsum",
                "amount" => -302.32,
                "assigned" => false,
                "ledgers" => [],
                "reference" => "16210229 7310",
                "document" => null,
                "no_document" => true,
                "contact" => [
                    "id" => 4,
                    "address" => "Jack Chavez",
                ],
                "finished" => false,
            ],
            [
                "id" => 3,
                "type" => 1,
                "date" => "2021-01-21",
                "description" => "id nunc",
                "amount" => -100.68,
                "assigned" => false,
                "ledgers" => [],
                "reference" => "16550728 6937",
                "document" => [
                    "link" => "#",
                    "name" => "B4M 6L9.pdf",
                ],
                "no_document" => false,
                "contact" => [
                    "id" => 4,
                    "address" => "Jack Chavez",
                ],
                "finished" => false,
            ],
            [
                "id" => 4,
                "type" => 1,
                "date" => "2021-01-31",
                "description" => "egestas Fusce",
                "amount" => -110.98,
                "assigned" => true,
                "ledgers" => [['id' => 8, 'description' => 'malesuada vel convallis'], ['id' => 9, 'description' => 'venenatis vel faucibus id']],
                "reference" => "16020301 9500",
                "document" => null,
                "no_document" => false,
                "contact" => null,
                "finished" => false,
            ],
            [
                "id" => 5,
                "type" => 1,
                "date" => "2021-02-08",
                "description" => "nunc feugiat Sed nec",
                "amount" => 117.56,
                "assigned" => true,
                "ledgers" => [['id' => 10, 'description' => 'ullamcorper Duis cursus diam at']],
                "reference" => "16301025 0508",
                "document" => [
                    "link" => "#",
                    "name" => "B7X 7P8.pdf",
                ],
                "no_document" => true,
                "contact" => null,
                "finished" => true,
            ],
            [
                "id" => 6,
                "type" => 2,
                "date" => "2021-02-18",
                "description" => "Quisque fringilla euismod",
                "amount" => 100.00,
                "assigned" => true,
                "ledgers" => [['id' => 10, 'description' => 'ullamcorper Duis cursus diam at']],
                "reference" => "R6-2021-4",
                "document" => [
                    "link" => "#",
                    "name" => "R6-2021-4.pdf",
                ],
                "no_document" => true,
                "contact" => [
                    "id" => null,
                    "address" => "Ciara Forbes\nP.O. Box 641, 6398 Accumsan Rd.\n22230 Cumberland",
                ],
                "finished" => true,
            ],
            [
                "id" => 7,
                "type" => 2,
                "date" => "2021-02-28",
                "description" => "Proin dolor Nulla semper",
                "amount" => 389.60,
                "assigned" => true,
                "ledgers" => [['id' => 2, 'description' => 'erat semper rutrum Fusce']],
                "reference" => "R7-2021-3",
                "document" => [
                    "link" => "#",
                    "name" => "R7-2021-3.pdf",
                ],
                "no_document" => false,
                "contact" => [
                    "id" => 2,
                    "address" => "Linus Zimmerman\n259-7856 Nisl Av.\n59756 Roccabruna",
                ],
                "finished" => true,
            ],
            [
                "id" => 8,
                "type" => 2,
                "date" => "2021-03-04",
                "description" => "scelerisque mollis",
                "amount" => 238.60,
                "assigned" => true,
                "ledgers" => [['id' => 4, 'description' => 'facilisis vitae orci Phasellus']],
                "reference" => "R8-2021-2",
                "document" => [
                    "link" => "#",
                    "name" => "R8-2021-2.pdf",
                ],
                "no_document" => true,
                "contact" => [
                    "id" => 3,
                    "address" => "Octavia Hampton\n784-5943 Id, St.\n53845 Schulen",
                ],
                "finished" => true,
            ],
            [
                "id" => 9,
                "type" => 2,
                "date" => "2021-03-14",
                "description" => "turpis Aliquam adipiscing",
                "amount" =>  146.22,
                "assigned" => false,
                "ledgers" => [],
                "reference" => "R9-2021-1",
                "document" => [
                    "link" => "#",
                    "name" => "R9-2021-1.pdf",
                ],
                "no_document" => false,
                "contact" => [
                    "id" => 1,
                    "address" => "Marvin Dennis\n7981 Commodo Road\n05903 Neiva",
                ],
                "finished" => true,
            ],
            [
                "id" => 10,
                "type" => 2,
                "date" => "2021-03-24",
                "description" => "amet consectetuer adipiscing",
                "amount" => -100.68,
                "assigned" => false,
                "ledgers" => [],
                "reference" => "R10-2021-0",
                "document" => null,
                "no_document" => true,
                "contact" => null,
                "finished" => false,
            ],
        ];
        $invoice = $invoices[$request->getQueryParams()['id'] - 1] ?? null;
        if (!$invoice) {
            return $this->helper->create(404, 'Beleg oder Rechnung nicht gefunden');
        }
        return $this->helper->create(200, $invoice);
    }
}
