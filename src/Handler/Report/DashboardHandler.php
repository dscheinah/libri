<?php

namespace App\Handler\Report;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class DashboardHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, [
            'accounts' => 494.38,
            'categories' => [
                [
                    'name' => 'Geschäftsbetrieb',
                    'amount' => 288.92,
                ],
                [
                    'name' => 'Ideeller Bereich',
                    'amount' => -187.50,
                ],
                [
                    'name' => 'Zweckbetrieb',
                    'amount' => 152.1,
                ],
                [
                    'name' => 'ohne Zuordnung',
                    'amount' => 240.86,
                ],
            ],
            'problems' => [
                [
                    'name' => 'Buchungen ohne Beleg',
                    'count' => 4,
                ],
                [
                    'name' => 'Offene Belege & Rechnungen',
                    'count' => 4,
                ],
                [
                    'name' => 'Fehlende Anhänge',
                    'count' => 1,
                ],
            ],
        ]);
    }
}
