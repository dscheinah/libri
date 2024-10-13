<?php

namespace App\Handler\Invoice;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceListHandler implements RequestHandlerInterface
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
                "reference" => "16611026 1549",
                "document" => true,
                "no_document" => true,
                "finished" => false,
            ],
            [
                "id" => 2,
                "type" => 1,
                "date" => "2021-01-11",
                "description" => "sem Pellentesque ut ipsum",
                "amount" => -302.32,
                "assigned" => false,
                "reference" => "16210229 7310",
                "document" => false,
                "no_document" => true,
                "finished" => false,
            ],
            [
                "id" => 3,
                "type" => 1,
                "date" => "2021-01-21",
                "description" => "id nunc",
                "amount" => -100.68,
                "assigned" => false,
                "reference" => "16550728 6937",
                "document" => true,
                "no_document" => false,
                "finished" => false,
            ],
            [
                "id" => 4,
                "type" => 1,
                "date" => "2021-01-31",
                "description" => "egestas Fusce",
                "amount" => -110.98,
                "assigned" => true,
                "reference" => "16020301 9500",
                "document" => false,
                "no_document" => false,
                "finished" => false,
            ],
            [
                "id" => 5,
                "type" => 1,
                "date" => "2021-02-08",
                "description" => "nunc feugiat Sed nec",
                "amount" => 117.56,
                "assigned" => true,
                "reference" => "16301025 0508",
                "document" => true,
                "no_document" => true,
                "finished" => true,
            ],
            [
                "id" => 6,
                "type" => 2,
                "date" => "2021-02-18",
                "description" => "Quisque fringilla euismod",
                "amount" => 100.00,
                "assigned" => true,
                "reference" => "R6-2021-4",
                "document" => true,
                "no_document" => true,
                "finished" => true,
            ],
            [
                "id" => 7,
                "type" => 2,
                "date" => "2021-02-28",
                "description" => "Proin dolor Nulla semper",
                "amount" => 389.60,
                "assigned" => true,
                "reference" => "R7-2021-3",
                "document" => true,
                "no_document" => false,
                "finished" => true,
            ],
            [
                "id" => 8,
                "type" => 2,
                "date" => "2021-03-04",
                "description" => "scelerisque mollis",
                "amount" => 238.60,
                "assigned" => true,
                "reference" => "R8-2021-2",
                "document" => true,
                "no_document" => true,
                "finished" => true,
            ],
            [
                "id" => 9,
                "type" => 2,
                "date" => "2021-03-14",
                "description" => "turpis Aliquam adipiscing",
                "amount" =>  146.22,
                "assigned" => false,
                "reference" => "R9-2021-1",
                "document" => true,
                "no_document" => false,
                "finished" => true,
            ],
            [
                "id" => 10,
                "type" => 2,
                "date" => "2021-03-24",
                "description" => "amet consectetuer adipiscing",
                "amount" => -100.68,
                "assigned" => false,
                "reference" => "R10-2021-0",
                "document" => false,
                "no_document" => true,
                "finished" => false,
            ],
        ];
        $queryParams = $request->getQueryParams();
        $type = $queryParams['type'] ?? null;
        if ($type) {
            $type = (int) $type;
            $invoices = array_filter($invoices, static function ($invoice) use ($type) {
                return $invoice['type'] === $type;
            });
        }
        $search = $queryParams['search'] ?? null;
        if ($search) {
            $search = mb_strtolower($search);
            $invoices = array_filter($invoices, static function ($invoice) use ($search) {
                return str_contains(mb_strtolower($invoice['description']), $search)
                    || str_contains(mb_strtolower($invoice['reference']), $search)
                    || str_contains((string) $invoice['id'], $search);
            });
        }
        return $this->helper->create(200, array_reverse(array_values($invoices)));
    }
}
