<?php

namespace App\Handler\Ledger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ledgers = [
            [
                'id' => 1,
                'date' => '2020-12-31',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '9000',
                    'description' => '9000',
                ],
                'description' => 'non lacinia at iaculis quis',
                'amount' => 240.86,
                'assigned' => true,
                'reference' => '16741106 8757',
                'canceled' => false,
            ],
            [
                'id' => 2,
                'date' => '2021-01-13',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '8000',
                    'description' => '8000 - Geschäftsbetrieb',
                ],
                'description' => 'erat semper rutrum Fusce',
                'amount' => 389.6,
                'assigned' => true,
                'reference' => '16331223 5900',
                'canceled' => false,
            ],
            [
                'id' => 3,
                'date' => '2021-01-24',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '2000',
                    'description' => '2000 - Ideeller Bereich',
                ],
                'description' => 'Phasellus libero mauris aliquam eu',
                'amount' => -302.32,
                'assigned' => false,
                'reference' => '16620705 6984',
                'canceled' => false,
            ],
            [
                'id' => 4,
                'date' => '2021-02-03',
                'account' => [
                    'no' => '1600',
                    'description' => '1600 - Kasse',
                ],
                'offset' => [
                    'no' => '6010',
                    'description' => '6010 - Eintrittsgelder',
                ],
                'description' => 'facilisis vitae orci Phasellus',
                'amount' => 238.6,
                'assigned' => true,
                'reference' => '16011116 4281',
                'canceled' => false,
            ],
            [
                'id' => 5,
                'date' => '2021-02-14',
                'account' => [
                    'no' => '1600',
                    'description' => '1600 - Kasse',
                ],
                'offset' => [
                    'no' => '2000',
                    'description' => '2000 - Ideeller Bereich',
                ],
                'description' => 'amet risus Donec',
                'amount' => 146.22,
                'assigned' => false,
                'reference' => '16311011 3465',
                'canceled' => false,
            ],
            [
                'id' => 6,
                'date' => '2021-02-25',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '8000',
                    'description' => '8000 - Geschäftsbetrieb',
                ],
                'description' => 'Nam nulla magna malesuada vel',
                'amount' => -100.68,
                'assigned' => false,
                'reference' => '16460117 1376',
                'canceled' => false,
            ],
            [
                'id' => 7,
                'date' => '2021-03-04',
                'account' => [
                    'no' => '1600',
                    'description' => '1600 - Kasse',
                ],
                'offset' => [
                    'no' => '2000',
                    'description' => '2000 - Ideeller Bereich',
                ],
                'description' => 'malesuada vel convallis',
                'amount' => -224.48,
                'assigned' => false,
                'reference' => '16690730 0427',
                'canceled' => true,
            ],
            [
                'id' => 8,
                'date' => '2021-03-04',
                'account' => [
                    'no' => '1600',
                    'description' => '1600 - Kasse',
                ],
                'offset' => [
                    'no' => '2000',
                    'description' => '2000 - Ideeller Bereich',
                ],
                'description' => 'malesuada vel convallis',
                'amount' => -24.48,
                'assigned' => true,
                'reference' => '16690730 0427',
                'canceled' => false,
            ],
            [
                'id' => 9,
                'date' => '2021-03-15',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '6000',
                    'description' => '6000 - Zweckbetrieb',
                ],
                'description' => 'venenatis vel faucibus id',
                'amount' => -86.5,
                'assigned' => true,
                'reference' => '16470507 1324',
                'canceled' => false,
            ],
            [
                'id' => 10,
                'date' => '2021-03-26',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '2000',
                    'description' => '2000 - Ideeller Bereich',
                ],
                'description' => 'ullamcorper Duis cursus diam at',
                'amount' => 217.56,
                'assigned' => true,
                'reference' => '16510328 0920',
                'canceled' => false,
            ],
            [
                'id' => 11,
                'date' => '2021-04-01',
                'account' => [
                    'no' => '1800',
                    'description' => '1800 - Bank',
                ],
                'offset' => [
                    'no' => '1600',
                    'description' => '1600 - Kasse',
                ],
                'description' => 'faucibus ullamcorper rutrum vel',
                'amount' => 52.12,
                'assigned' => false,
                'reference' => '',
                'canceled' => false,
            ],
        ];
        $queryParams = $request->getQueryParams();
        $account = $queryParams['account'] ?? null;
        if ($account) {
            $ledgers = array_filter($ledgers, static function ($ledger) use ($account) {
                return $ledger['account']['no'] === $account;
            });
        }
        $search = $queryParams['search'] ?? null;
        if ($search) {
            $search = mb_strtolower($search);
            $ledgers = array_filter($ledgers, static function ($ledger) use ($search) {
                return str_contains(mb_strtolower($ledger['description']), $search)
                    || str_contains(mb_strtolower($ledger['reference']), $search)
                    || str_contains((string) $ledger['id'], $search);
            });
        }
        return $this->helper->create(200, array_reverse(array_values($ledgers)));
    }
}
