<?php

namespace App\Repository;

use App\Helper\Pdf;
use App\Storage\AssignmentStorage;
use App\Storage\InvoiceStorage;
use App\Storage\MasterStorage;
use DomainException;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Use this repository to handle invoice management, assignments to ledgers, and PDF generation.
 */
class InvoiceRepository
{
    public function __construct(
        private readonly InvoiceStorage $storage,
        private readonly AssignmentStorage $assignmentStorage,
        private readonly MasterStorage $masterStorage,
    ) {
    }

    /**
     * Retrieves a list of invoices based on type and optional search term.
     * Use this method to display filtered invoice lists (e.g., incoming/outgoing).
     *
     * @param int    $type   The type of invoices (e.g., income/expense).
     * @param string $search Optional search term to filter results.
     *
     * @return list<mixed> A list of invoice data arrays.
     */
    public function listInvoices(int $type, string $search): array
    {
        $invoices = [];
        foreach ($search ? $this->storage->fetchSome($type, $search) : $this->storage->fetchAll($type) as $invoice) {
            assert(is_array($invoice));
            $invoices[] = [
                'id' => (int) $invoice['id'],
                'type' => (int) $invoice['type'],
                'date' => $invoice['date'],
                'description' => $invoice['description'],
                'amount' => (float) $invoice['amount'],
                'assigned' => (bool) $invoice['closed'],
                'reference' => $invoice['reference'] ?? null,
                'document' => (bool) $invoice['has_document'],
                'no_document' => (bool) $invoice['no_document'],
                'finished' => (bool) $invoice['finished'],
            ];
        }
        return $invoices;
    }

    /**
     * Retrieves a list of open (unassigned) invoices.
     * Use this method for selecting invoices that need to be assigned to ledger entries.
     *
     * @return list<mixed> A list of open invoice data.
     */
    public function listOpenInvoices(): array
    {
        $invoices = [];
        foreach ($this->storage->fetchOpen() as $invoice) {
            assert(is_array($invoice));
            $invoices[] = [
                'id' => (int) $invoice['id'],
                'type' => (int) $invoice['type'],
                'date' => $invoice['date'],
                'description' => $invoice['description'],
                'amount' => (float) $invoice['amount'],
                'reference' => $invoice['reference'],
            ];
        }
        return $invoices;
    }

    /**
     * Retrieves a single invoice by its ID, including assigned ledgers and document info.
     * Use this method for viewing or editing invoice details.
     *
     * @param int $id The unique identifier of the invoice.
     *
     * @return array<string, mixed>|null The invoice data or null if not found.
     */
    public function getInvoice(int $id): ?array
    {
        $invoice = $this->storage->fetchOne($id);
        if (!$invoice) {
            return null;
        }
        $document = $invoice['document'] ? [
            'link' => 'data:application/pdf;base64,' . base64_encode((string) $invoice['document']),
            'name' => $invoice['document_name'] ?? '- Dokument -',
        ] : null;
        $contact = array_filter([
            'id' => $invoice['contact_id'] ?? null,
            'address' => $invoice['contact_address'] ?? null,
        ]);
        return [
            'id' => (int) $invoice['id'],
            'type' => (int) $invoice['type'],
            'date' => $invoice['date'],
            'description' => $invoice['description'],
            'amount' => (float) $invoice['amount'],
            'assigned' => (bool) $invoice['closed'],
            'ledgers' => iterator_to_array($this->assignmentStorage->fetchAssignedLedgersForInvoice($id)),
            'reference' => $invoice['reference'] ?? null,
            'document' => $document,
            'no_document' => (bool) $invoice['no_document'],
            'contact' => $contact ?: null,
            'finished' => (bool) $invoice['finished'],
        ];
    }

    /**
     * Finalizes an invoice by generating a PDF document if not already present.
     * Use this method when an invoice is ready to be sent.
     *
     * @param int $id The unique identifier of the invoice to finish.
     *
     * @return bool True on success, false otherwise.
     */
    public function finishInvoice(int $id): bool
    {
        $invoice = $this->storage->fetchOpenInvoice($id);
        if (!$invoice) {
            return false;
        }

        $master = array_column(iterator_to_array($this->masterStorage->fetchAllValues()), 'value', 'key');

        $addressShort = str_replace("\n", ' - ', trim($master['address'] ?? ''));

        $address = str_replace("\n", '<br>', trim($master['address'] ?? ''));
        $account = str_replace("\n", '<br>', trim($master['account'] ?? ''));

        $contactAddress = str_replace("\n", '<br>', trim((string) $invoice['contact_address']));
        $timestamp = strtotime((string) $invoice['date']) ?: null;
        $amount = number_format((float) $invoice['amount'], 2, ',', '.');

        $replacements = [
            '#nummer#' => $invoice['id'],
            '#jahr#' => date('Y', $timestamp),
            '#monat#' => date('m', $timestamp),
            '#tag#' => date('d', $timestamp),
            '#checksum#' => 100 - ((int) $invoice['id'] % 100),
            '##' => '#',
        ];
        $number = str_replace(array_keys($replacements), $replacements, $master['number'] ?? '#nummer#');

        $file = "$number.pdf";

        $pdf = new Pdf($file);
        $pdf->addHTMLCell(
            "
                <style>
                    small {
                        color: gray;
                    }
                </style>
                <small>$addressShort</small>
            ",
            25, 45, 80, 11.7
        );
        $pdf->addHTMLCell($contactAddress, 25, 45 + 11.7, 80, 27);
        $pdf->addHTMLCell(
            'Datum: ' . date('d.m.Y', $timestamp),
            125, 50 + 45 - 4, 75, 4,
        );
        $pdf->addHTMLCell(
        "
                <style>
                    table {
                        border: 1px solid lightgray;
                    }
                    
                    th, td {
                        padding: 2mm;
                    }
                </style>
                <h1>Rechnung Nr. $number</h1>
                <p>
                    Sehr geehrte Damen und Herren,<br>
                    <br>
                    vielen Dank für Ihren Auftrag.<br>
                    Hiermit stelle ich Ihnen die folgende Leistung in Rechnung:
                </p>
                <table>
                    <colgroup>
                        <col style='width: 100mm;'/>
                        <col style='width: 65mm;'/>
                    </colgroup>
                    <thead>
                        <tr><th><strong>Beschreibung</strong></th><th><strong>Betrag</strong></th></tr>                    
                    </thead>
                    <tbody>
                        <tr><td>{$invoice['description']}</td><td>$amount €</td></tr>                    
                    </tbody>
                </table>
                <p>
                    Bitte überweisen Sie den Rechnungsbetrag innerhalb von 14 Tagen unter Angabe der Rechnungsnummer.<br>
                    <br>
                    Vielen Dank und viele Grüße
                </p>
            ",
            25, 105, 210 - 25 - 20, 105,
        );
        $pdf->addHTMLCell("<p>$address</p>", 25, 297 - 87 + 8.46, 75, 87 - 8.46);
        $pdf->addHTMLCell("<p>$account</p>", 25 + 75 + 15, 297 - 87 + 8.46, 75, 87 - 8.46);

        $this->storage->updateReference($id, $number);
        $this->storage->updateDocument($id, $file, $pdf->getOutPDFString());
        $this->storage->markFinished($id);
        return true;
    }

    /**
     * Removes an open invoice.
     * Use this method to delete an invoice that has not yet been finalized or assigned.
     *
     * @param int $id The unique identifier of the invoice.
     *
     * @return bool True if successful.
     */
    public function removeInvoice(int $id): bool
    {
        return (bool) $this->storage->removeOpenInvoice($id);
    }

    /**
     * Saves or updates an invoice.
     * Handles creation of new invoices or updating details of existing ones.
     * Supports updating basic details even for closed/finished invoices.
     *
     * @param array<string, int|string> $data     The invoice data.
     * @param UploadedFileInterface|null $document Optional uploaded document (PDF).
     *
     * @return bool True on success, false otherwise.
     */
    public function saveInvoice(array $data, ?UploadedFileInterface $document = null): bool
    {
        $contactId = ((int) ($data['contact_id'] ?? 0)) ?: null;

        if ($data['id'] ?? null) {
            $id = (int) $data['id'];

            $invoice = $this->storage->fetchOne($id);
            if (!$invoice) {
                return false;
            }
            if ($invoice['closed'] || $invoice['finished']) {
                $this->storage->updateDetails(
                    $id,
                    (string) ($data['description'] ?? ''),
                    (string) ($data['reference'] ?? ''),
                    (bool) ($data['no_document'] ?? false),
                    $contactId,
                );
            } else {
                $this->storage->updateAll(
                    $id,
                    (string) ($data['date'] ?? ''),
                    (float) ($data['amount'] ?? 0.0),
                    (string) ($data['description'] ?? ''),
                    (string) ($data['reference'] ?? ''),
                    (bool) ($data['no_document'] ?? false),
                    (string) ($data['contact_address'] ?? ''),
                    $contactId,
                );
            }
        } else {
            if (!($data['type'] ?? null)) {
                return false;
            }
            $id = $this->storage->create(
                (int) $data['type'],
                (string) ($data['date'] ?? ''),
                (float) ($data['amount'] ?? 0.0),
                (string) ($data['description'] ?? ''),
                (string) ($data['reference'] ?? ''),
                (bool) ($data['no_document'] ?? false),
                (string) ($data['contact_address'] ?? ''),
                $contactId,
            );
        }

        if ($document) {
            $this->storage->updateDocument($id, (string) $document->getClientFilename(), $document->getStream());
        }

        return true;
    }

    /**
     * Assigns one or more ledger entries to an invoice.
     * Ensures that the total amount of ledgers matches the invoice amount.
     * Both ledgers and invoice are marked as closed upon successful assignment.
     *
     * @param int       $invoiceId The ID of the invoice.
     * @param list<int> $ledgerIds  List of ledger entry IDs to assign.
     *
     * @throws DomainException If the invoice or a ledger does not exist, or if amounts don't match.
     */
    public function assignLedgers(int $invoiceId, array $ledgerIds): void
    {
        $this->assignmentStorage->transactional(function () use ($invoiceId, $ledgerIds) {
            $invoice = $this->assignmentStorage->fetchOpenInvoice($invoiceId);
            if (!$invoice) {
                throw new DomainException('invoice does not exist', 400);
            }
            $amount = 0.0;
            foreach ($ledgerIds as $ledgerId) {
                $ledger = $this->assignmentStorage->fetchOpenLedger($ledgerId);
                if (!$ledger) {
                    throw new DomainException('ledger does not exist', 400);
                }
                $this->assignmentStorage->createAssignment($ledgerId, $invoiceId);
                $this->assignmentStorage->markLedgerClosed($ledgerId);
                $amount += $ledger['amount'];
            }
            assert(is_numeric($invoice['amount']));
            if ((float) $invoice['amount'] !== $amount) {
                throw new DomainException('amounts do not match', 400);
            }
            $this->assignmentStorage->markInvoiceClosed($invoiceId);
            return true;
        });
    }
}
