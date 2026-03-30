<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceRemoveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        if (!$this->repository->removeInvoice((int) $id)) {
            return $this->helper->create(500, 'Darf nicht gelöscht werden');
        }
        return $this->helper->create(204);
    }
}
