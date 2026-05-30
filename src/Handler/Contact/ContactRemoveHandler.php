<?php

namespace App\Handler\Contact;

use App\Repository\ContactRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for removing a contact.
 */
class ContactRemoveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly ContactRepository $repository,
    ) {
    }

    /**
     * Handles removing a contact by its 'id' query parameter.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        $this->repository->removeContact((int) $id);
        return $this->helper->create(204);
    }
}
