<?php

namespace App\Handler\Ledger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerSaveAssignHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(204);
    }
}
