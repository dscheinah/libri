<?php

namespace App\Handler\Contact;

use App\Repository\ContactRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler to retrieve a list of contacts, optionally filtered by a search term.
 */
class ContactListHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly ContactRepository $repository,
    ) {
    }

    /**
     * Handles the request to list contacts.
     * Supports a 'search' query parameter for filtering.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $search = $request->getQueryParams()['search'] ?? null;
        return $this->helper->create(200, $this->repository->listContacts((string) $search));
    }
}
