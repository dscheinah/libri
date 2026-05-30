<?php

namespace App\Handler\Account;

use App\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler to retrieve a list of all accounts.
 */
class AccountListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly AccountRepository $repository,
    ) {
    }

    /**
     * Handles the request to list all accounts.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, $this->repository->listAccounts(false));
    }
}
