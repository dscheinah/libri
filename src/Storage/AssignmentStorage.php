<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class AssignmentStorage extends Storage
{
    /**
     * Fetches an open ledger entry by ID and locks it for update.
     *
     * @param int $id The ledger entry ID.
     *
     * @return array<mixed>|null The ledger data or null if not found or already closed/canceled/transfer.
     */
    public function fetchOpenLedger(int $id): ?array
    {
        $ledger = $this->fetch(
            'SELECT `id`, `amount` FROM `ledgers` 
                WHERE `id` = ? AND `canceled` = false AND `closed` = false AND `transfer` = false
                FOR UPDATE',
            [$id]
        )->current();
        if ($ledger) {
            assert(is_array($ledger));
            return $ledger;
        }
        return null;
    }

    /**
     * Fetches an open invoice by ID and locks it for update.
     *
     * @param int $id The invoice ID.
     *
     * @return array<mixed>|null The invoice data or null if not found or already closed.
     */
    public function fetchOpenInvoice(int $id): ?array
    {
        $invoice = $this->fetch(
            'SELECT `id`, `amount` FROM `invoices` 
                WHERE `id` = ? AND `closed` = false AND (`type` = 1 OR `finished` = true)
                FOR UPDATE',
            [$id]
        )->current();
        if ($invoice) {
            assert(is_array($invoice));
            return $invoice;
        }
        return null;
    }

    /**
     * Creates a new assignment between a ledger entry and an invoice.
     *
     * @param int $ledgerId  The ledger entry ID.
     * @param int $invoiceId The invoice ID.
     */
    public function createAssignment(int $ledgerId, int $invoiceId): void
    {
        $this->execute(
            'INSERT INTO `ledgers_x_invoices` (`ledger_id`, `invoice_id`) VALUES (?, ?)',
            [$ledgerId, $invoiceId]
        );
    }

    /**
     * Marks a ledger entry as closed (assigned).
     *
     * @param int $ledgerId The ledger entry ID.
     */
    public function markLedgerClosed(int $ledgerId): void
    {
        $this->execute('UPDATE `ledgers` SET `closed` = 1 WHERE `id` = ?', [$ledgerId]);
    }

    /**
     * Marks an invoice as closed (assigned).
     *
     * @param int $invoiceId The invoice ID.
     */
    public function markInvoiceClosed(int $invoiceId): void
    {
        $this->execute('UPDATE `invoices` SET `closed` = 1 WHERE `id` = ?', [$invoiceId]);
    }

    /**
     * Fetches all invoices assigned to a specific ledger entry.
     *
     * @param int $id The ledger entry ID.
     *
     * @return Generator<int, array<string, int|string>> Yields invoice data arrays.
     */
    public function fetchAssignedInvoicesForLedger(int $id): Generator
    {
        return $this->fetch(
            'SELECT i.`id`, i.`description` FROM `invoices` i 
                INNER JOIN `ledgers_x_invoices` x ON i.`id` = x.`invoice_id` WHERE x.`ledger_id` = ?',
            [$id]
        );
    }

    /**
     * Fetches all ledger entries assigned to a specific invoice.
     *
     * @param int $id The invoice ID.
     *
     * @return Generator<int, array<string, int|string>> Yields ledger data arrays.
     */
    public function fetchAssignedLedgersForInvoice(int $id): Generator
    {
        return $this->fetch(
            'SELECT l.`id`, l.`description` FROM `ledgers` l 
                INNER JOIN `ledgers_x_invoices` x ON l.`id` = x.`ledger_id` WHERE x.`invoice_id` = ?',
            [$id]
        );
    }
}
