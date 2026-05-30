<?php

namespace App\Handler\Contact;

use App\Repository\ContactRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for saving (creating or updating) a contact.
 */
class ContactSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly ContactRepository $repository,
    ) {
    }

    /**
     * Handles saving contact data from the request body.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repository->saveContact($data);
        return $this->helper->create(204);
    }
}
