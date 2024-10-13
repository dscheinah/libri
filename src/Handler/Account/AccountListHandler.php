<?php

namespace App\Handler\Account;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class AccountListHandler implements RequestHandlerInterface
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
            [
                'no' => '2000',
                'name' => 'Ideeller Bereich',
                'category' => 2,
                'real' => false,
            ],
            [
                'no' => '6000',
                'name' => 'Zweckbetrieb',
                'category' => 3,
                'real' => false,
            ],
            [
                'no' => '6010',
                'name' => 'Eintrittsgelder',
                'category' => 3,
                'real' => false,
            ],
            [
                'no' => '8000',
                'name' => 'Geschäftsbetrieb',
                'category' => 1,
                'real' => false,
            ],
            [
                'no' => '9000',
                'name' => '',
                'category' => null,
                'real' => false,
            ],
        ]);
    }
}
