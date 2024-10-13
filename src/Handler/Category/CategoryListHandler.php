<?php

namespace App\Handler\Category;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class CategoryListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, [
            [
                'id' => 1,
                'name' => 'Geschäftsbetrieb',
            ],
            [
                'id' => 2,
                'name' => 'Ideeller Bereich',
            ],
            [
                'id' => 3,
                'name' => 'Zweckbetrieb',
            ],
        ]);
    }
}
