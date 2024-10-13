<?php

namespace App\Handler\Account;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class AccountListRealHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, [
            [
                'no' => '1600',
                'name' => 'Kasse',
                'category' => null,
                'real' => true,
            ],
            [
                'no' => '1800',
                'name' => 'Bank',
                'category' => null,
                'real' => true,
            ],
        ]);
    }
}
