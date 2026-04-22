<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class InvoiceSaveAssignHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        $id = $data['id'] ?? null;
        if (!$id) {
            return $this->helper->create(400);
        }

        if (!isset($data['ledgers'])) {
            return $this->helper->create(400);
        }
        $ledgers = array_map(intval(...), (array) $data['ledgers']);

        $this->repository->assignLedgers($id, $ledgers);

        return $this->helper->create(204);
    }
}
