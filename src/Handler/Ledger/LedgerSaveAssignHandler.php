<?php

namespace App\Handler\Ledger;

use App\Repository\LedgerRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerSaveAssignHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly LedgerRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        $id = $data['id'] ?? null;
        if (!$id) {
            return $this->helper->create(400);
        }

        if (!isset($data['invoices'])) {
            return $this->helper->create(400);
        }
        $invoices = array_map(intval(...), (array) $data['invoices']);

        $this->repository->assignInvoices($id, $invoices);

        return $this->helper->create(204);
    }
}
