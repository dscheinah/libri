<?php

namespace App\Handler\Ledger;

use App\Repository\LedgerRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly LedgerRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        $ledger = $this->repository->getLedger((int) $id);
        if (!$ledger) {
            return $this->helper->create(404, 'Buchung nicht gefunden');
        }
        return $this->helper->create(200, $ledger);
    }
}
