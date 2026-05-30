<?php

namespace App\Handler\Ledger;

use App\Repository\LedgerRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler to retrieve a list of ledger entries, filtered by account and optionally by a search term.
 */
class LedgerListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly LedgerRepository $repository,
    ) {
    }

    /**
     * Handles the request to list ledger entries.
     * Expects 'account' and optionally 'search' query parameters.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $account = $queryParams['account'] ?? null;
        $search = $queryParams['search'] ?? null;
        return $this->helper->create(200, $this->repository->listLedgers((string) $account, (string) $search));
    }
}
