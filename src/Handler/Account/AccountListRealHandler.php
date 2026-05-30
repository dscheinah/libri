<?php

namespace App\Handler\Account;

use App\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for retrieving a list of real (physical/bank) accounts.
 */
class AccountListRealHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly AccountRepository $repository,
    ) {
    }

    /**
     * Handles the request to list all real accounts.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, $this->repository->listAccounts(true));
    }
}
