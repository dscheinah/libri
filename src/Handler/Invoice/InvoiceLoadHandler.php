<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for loading a single invoice by ID.
 */
class InvoiceLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    /**
     * Handles loading an invoice by its 'id' query parameter.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        $invoice = $this->repository->getInvoice((int) $id);
        if (!$invoice) {
            return $this->helper->create(404, 'Beleg oder Rechnung nicht gefunden');
        }
        return $this->helper->create(200, $invoice);
    }
}
