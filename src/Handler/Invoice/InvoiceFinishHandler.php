<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for finalizing an invoice (marking it as finished).
 */
class InvoiceFinishHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    /**
     * Handles finalizing an invoice by its 'id' query parameter.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        if (!$this->repository->finishInvoice((int) $id)) {
            return $this->helper->create(500, 'Darf nicht finalisiert werden');
        }
        return $this->helper->create(204);
    }
}
