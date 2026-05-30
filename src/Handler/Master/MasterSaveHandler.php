<?php

namespace App\Handler\Master;

use App\Repository\MasterRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler to save master data (system settings).
 */
class MasterSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly MasterRepository $repository,
    ) {
    }

    /**
     * Handles the request to save master data.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repository->storeEntries($data);
        return $this->helper->create(204);
    }
}
