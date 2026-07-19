<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class InvoiceStorage extends Storage
{
    /**
     * Counts all invoices that have not been assigned to any ledger entry.
     *
     * @return int The count of unassigned invoices.
     */
    public function countUnassigned(): int
    {
        $result = $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `invoices` WHERE `closed` = false'
        )->current();
        if ($result) {
            assert(is_array($result));
            return $result['count'] ?? 0;
        }
        return 0;
    }

    /**
     * Counts all invoices that are missing a document (PDF) and are not marked as "no document".
     *
     * @return int The count of invoices without documents.
     */
    public function countWithoutDocument(): int
    {
        $result = $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `invoices`
            WHERE `document` IS NULL AND `no_document` = false AND `finished` = false'
        )->current();
        if ($result) {
            assert(is_array($result));
            return $result['count'] ?? 0;
        }
        return 0;
    }

    /**
     * Fetches all invoices for a specific type.
     *
     * @param int $type The invoice type.
     *
     * @return Generator<int, array<string, int|string|float>> Yields invoice data arrays.
     */
    public function fetchAll(int $type): Generator
    {
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `closed`, `reference`, `no_document`, `finished`, 
                    `document` IS NOT NULL AS `has_document` 
                FROM `invoices` 
                WHERE `type` = ? 
                ORDER BY `date` DESC',
            [$type]
        );
    }

    /**
     * Fetches invoices for a specific type matching a search term.
     *
     * @param int    $type   The invoice type.
     * @param string $search The search term.
     *
     * @return Generator<int, array<string, int|string|float>> Yields matching invoice data arrays.
     */
    public function fetchSome(int $type, string $search): Generator
    {
        $search = '%' . $search . '%';
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `closed`, `reference`, `no_document`, `finished`, 
                    `document` IS NOT NULL AS `has_document` 
                FROM `invoices` 
                WHERE `type` = ? AND (`id` LIKE ? OR `description` LIKE ? OR `reference` LIKE ?) 
                ORDER BY `date` DESC',
            [$type, $search, $search, $search]
        );
    }

    /**
     * Fetches all unassigned invoices that are either income or already finished.
     * Use this method to retrieve invoices ready for assignment to ledger entries.
     *
     * @return Generator<int, array<string, int|string|float>> Yields open invoice data arrays.
     */
    public function fetchOpen(): Generator
    {
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `reference` FROM `invoices` 
                WHERE `closed` = false AND (`type` = 1 OR `finished` = true)'
        );
    }

    /**
     * Fetches a single invoice by its ID.
     *
     * @param int $id The invoice ID.
     *
     * @return array<string, int|string|null|float>|null The invoice data or null if not found.
     */
    public function fetchOne(int $id): ?array
    {
        $invoice = $this->fetch('SELECT * FROM `invoices` WHERE `id` = ?', [$id])->current();
        if ($invoice) {
            assert(is_array($invoice));
            return $invoice;
        }
        return null;
    }

    /**
     * Fetches an open, non-finished invoice by its ID.
     *
     * @param int $id The invoice ID.
     *
     * @return array<string, int|string|null|float>|null The invoice data or null if not found.
     */
    public function fetchOpenInvoice(int $id): ?array
    {
        $invoice = $this->fetch(
            'SELECT * FROM `invoices` WHERE `id` = ? AND `closed` = false AND `finished` = false',
            [$id]
        )->current();
        if ($invoice) {
            assert(is_array($invoice));
            return $invoice;
        }
        return null;
    }

    /**
     * Removes an open, non-finished invoice by its ID.
     *
     * @param int $id The invoice ID.
     *
     * @return int The number of affected rows.
     */
    public function removeOpenInvoice(int $id): int
    {
        return $this->execute(
            'DELETE FROM `invoices` WHERE `id` = ? AND `closed` = false AND `finished` = false',
            [$id]
        );
    }

    /**
     * Creates a new invoice.
     *
     * @param int         $type           The invoice type (income/expense).
     * @param string      $date           Invoice date.
     * @param float       $amount         Invoice amount.
     * @param string      $description    Description.
     * @param string      $reference      Reference/Invoice number.
     * @param bool        $noDocument     Whether no document is expected.
     * @param string      $contactAddress Address of the contact.
     * @param int|null    $contactId      ID of the associated contact.
     *
     * @return int The ID of the newly created invoice.
     */
    public function create(
        int $type,
        string $date,
        float $amount,
        string $description,
        string $reference,
        bool $noDocument,
        string $contactAddress,
        ?int $contactId,
    ): int {
        return $this->insert(
            'INSERT INTO `invoices` 
                (`type`, `date`, `amount`, `description`, `reference`, `no_document`, `contact_address`, `contact_id`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$type, $date, $amount, $description, $reference, $noDocument, $contactAddress, $contactId]
        );
    }

    /**
     * Updates all fields of an invoice.
     * Use this for comprehensive editing of an invoice.
     *
     * @param int         $id             The invoice ID.
     * @param string      $date           Invoice date.
     * @param float       $amount         Invoice amount.
     * @param string      $description    Description.
     * @param string      $reference      Reference/Invoice number.
     * @param bool        $noDocument     Whether no document is expected.
     * @param string      $contactAddress Address of the contact.
     * @param int|null    $contactId      ID of the associated contact.
     */
    public function updateAll(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $reference,
        bool $noDocument,
        string $contactAddress,
        ?int $contactId,
    ): void {
        $this->execute(
            'UPDATE `invoices` SET 
                `date` = ?, `amount` = ?, `description` = ?, `reference` = ?, `no_document` = ?, `contact_address` = ?, `contact_id` = ?
                WHERE `id` = ?',
            [$date, $amount, $description, $reference, $noDocument, $contactAddress, $contactId, $id]
        );
    }

    /**
     * Updates only non-financial details of an invoice.
     * Use this when the invoice is already closed or finished, where amount/date shouldn't change.
     *
     * @param int      $id          The invoice ID.
     * @param string   $description Description.
     * @param string   $reference   Reference/Invoice number.
     * @param bool     $noDocument  Whether no document is expected.
     * @param int|null $contactId   ID of the associated contact.
     */
    public function updateDetails(
        int $id,
        string $description,
        string $reference,
        bool $noDocument,
        ?int $contactId,
    ): void {
        $this->execute(
            'UPDATE `invoices` SET 
                `description` = ?, `reference` = ?, `no_document` = ?, `contact_id` = ?
                WHERE `id` = ?',
            [$description, $reference, $noDocument, $contactId, $id]
        );
    }

    /**
     * Updates the reference (invoice number) of an invoice.
     *
     * @param int    $id        The invoice ID.
     * @param string $reference The new reference.
     */
    public function updateReference(int $id, string $reference): void
    {
        $this->execute('UPDATE `invoices` SET `reference` = ? WHERE `id` = ?', [$reference, $id]);
    }

    /**
     * Updates/attaches a document to an invoice.
     *
     * @param int    $id      The invoice ID.
     * @param string $name    The document filename.
     * @param string $content The document content (binary).
     */
    public function updateDocument(int $id, string $name, string $content): void
    {
        $this->execute(
            'UPDATE `invoices` SET `document_name` = ?, `document` = ? WHERE `id` = ?',
            [$name, $content, $id]
        );
    }

    /**
     * Fetches invoices within a date range for reporting.
     *
     * @param string $start Start date.
     * @param string $end   End date.
     *
     * @return Generator<int, array<string, int|string|float>> Yields invoice data arrays.
     */
    public function fetchForReport(string $start, string $end): Generator
    {
        return $this->fetch('SELECT 
                    `id`,
                    `date`,
                    `description`,
                    `reference`,
                    `amount`,
                    `no_document`, 
                    `document_name`,
                    `document`
                FROM `invoices` WHERE `date` BETWEEN ? AND ?',
            [$start, $end]
        );
    }

    /**
     * Fetches all unassigned invoices.
     * Similar to fetchOpen() but provides more columns for detailed lists.
     *
     * @return Generator<int, array<string, int|string|float>> Yields unassigned invoice data arrays.
     */
    public function fetchUnassigned(): Generator
    {
        return $this->fetch('SELECT 
                    `id`,
                    `date`,
                    `description`,
                    `reference`,
                    `amount`,
                    `no_document`, 
                    `document_name`,
                    `document`
                FROM `invoices` WHERE `closed` = false',
        );
    }

    /**
     * Fetches all invoices that are missing a document.
     *
     * @return Generator<int, array<string, int|string|float>> Yields invoice data arrays without documents.
     */
    public function fetchWithoutDocument(): Generator
    {
        return $this->fetch('SELECT 
                    `id`,
                    `date`,
                    `description`,
                    `reference`,
                    `amount`,
                    `no_document`, 
                    `document_name`,
                    `document`
                FROM `invoices` WHERE `document` IS NULL AND `no_document` = false AND `finished` = false',
        );
    }

    /**
     * Finalizes an invoice by marking it as finished.
     *
     * @param int $id The invoice ID.
     */
    public function markFinished(int $id): void
    {
        $this->execute('UPDATE `invoices` SET `finished` = 1 WHERE `id` = ?', [$id]);
    }
}
