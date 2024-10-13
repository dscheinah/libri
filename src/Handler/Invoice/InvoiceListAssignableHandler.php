<?php

namespace App\Handler\Invoice;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceListAssignableHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $invoices = [
            [
                "id" => 2,
                "type" => 1,
                "date" => "2021-01-11",
                "description" => "sem Pellentesque ut ipsum",
                "amount" => -302.32,
                "reference" => "16210229 7310",
            ],
            [
                "id" => 3,
                "type" => 1,
                "date" => "2021-01-21",
                "description" => "id nunc",
                "amount" => -100.68,
                "reference" => "16550728 6937",
            ],
            [
                "id" => 9,
                "type" => 2,
                "date" => "2021-03-14",
                "description" => "turpis Aliquam adipiscing",
                "amount" =>  146.22,
                "reference" => "R9-2021-1",
            ],
        ];
        return $this->helper->create(200, $invoices);
    }
}
