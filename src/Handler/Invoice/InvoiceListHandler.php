<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $type = $queryParams['type'] ?? null;
        $search = $queryParams['search'] ?? null;
        return $this->helper->create(200, $this->repository->listInvoices((int) $type, (string) $search));
    }
}
