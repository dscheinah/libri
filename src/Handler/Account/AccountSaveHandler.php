<?php

namespace App\Handler\Account;

use App\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for updating the account list.
 */
class AccountSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly AccountRepository $repository,
    ) {
    }

    /**
     * Handles the request to save/synchronize all accounts.
     * Expects parallel arrays of account numbers, names, and categories in the POST body.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        if (!isset($data['no'], $data['name'], $data['category'], $data['real'])) {
            return $this->helper->create(400);
        }

        $nos = array_values((array) $data['no']);
        $names = array_values((array) $data['name']);
        $categories = array_values((array) $data['category']);
        if (count($nos) !== count($names) || count($nos) !== count($categories)) {
            return $this->helper->create(400);
        }

        $reals = (array) $data['real'];

        $this->repository->updateAccounts($nos, $names, $categories, $reals);
        return $this->helper->create(204);
    }
}
