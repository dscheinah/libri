<?php

namespace App\Handler\Report;

use App\Repository\DashboardRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class DashboardHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly DashboardRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, [
            'accounts' => $this->repository->accounts(),
            'categories' => $this->repository->categories(),
            'problems' => $this->repository->problems(),
        ]);
    }
}
