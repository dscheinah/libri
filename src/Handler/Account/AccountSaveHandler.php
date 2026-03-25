<?php

namespace App\Handler\Account;

use App\Repository\AccountRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class AccountSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly AccountRepository $repository,
    ) {
    }

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
