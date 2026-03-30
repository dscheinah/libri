<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        if (!$this->repository->saveInvoice($data)) {
            return $this->helper->create(400, 'Fehlerhafte Daten');
        }
        return $this->helper->create(204);
    }
}
