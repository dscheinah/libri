<?php

namespace App\Handler\Ledger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerListAssignableHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ledgers = [
            [
                'id' => 3,
                'date' => '2021-01-24',
                'description' => 'Phasellus libero mauris aliquam eu',
                'amount' => -302.32,
                'reference' => '16620705 6984',
            ],
            [
                'id' => 5,
                'date' => '2021-02-14',
                'description' => 'amet risus Donec',
                'amount' => 146.22,
                'reference' => '16311011 3465',
            ],
            [
                'id' => 6,
                'date' => '2021-02-25',
                'description' => 'Nam nulla magna malesuada vel',
                'amount' => -100.68,
                'reference' => '16460117 1376',
            ],
            [
                'id' => 11,
                'date' => '2021-04-01',
                'description' => 'faucibus ullamcorper rutrum vel',
                'amount' => 52.12,
                'reference' => '',
            ],
        ];
        return $this->helper->create(200, $ledgers);
    }
}
