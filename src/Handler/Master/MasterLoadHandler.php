<?php

namespace App\Handler\Master;

use App\Repository\MasterRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler to load master data (system settings).
 */
class MasterLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly MasterRepository $repository,
    ) {
    }

    /**
     * Handles the request to load master data.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->helper->create(200, $this->repository->loadEntries());
    }
}
