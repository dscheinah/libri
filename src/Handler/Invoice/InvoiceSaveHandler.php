<?php

namespace App\Handler\Invoice;

use App\Repository\InvoiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Handler for saving invoice data and its associated document.
 */
class InvoiceSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly InvoiceRepository $repository,
    ) {
    }

    /**
     * Handles saving invoice data and uploading a document.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        $document = $request->getUploadedFiles()['document'] ?? null;
        assert($document === null || $document instanceof UploadedFileInterface);
        if ($document?->getSize() === 0) {
            $document = null;
        }

        if (!$this->repository->saveInvoice($data, $document)) {
            return $this->helper->create(400, 'Fehlerhafte Daten');
        }
        return $this->helper->create(204);
    }
}
