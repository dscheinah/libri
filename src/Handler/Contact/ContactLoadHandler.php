<?php

namespace App\Handler\Contact;

use App\Repository\ContactRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for loading a single contact by ID.
 */
class ContactLoadHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly ContactRepository $repository,
    ) {
    }

    /**
     * Handles loading a contact by its 'id' query parameter.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? 0;
        $contact = $this->repository->getContact((int) $id);
        if (!$contact) {
            return $this->helper->create(404, 'Kontakt nicht gefunden');
        }
        return $this->helper->create(200, $contact);
    }
}
