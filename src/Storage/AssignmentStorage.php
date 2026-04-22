<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class AssignmentStorage extends Storage
{
    /**
     * @return array<mixed>|null
     */
    public function fetchOpenLedger(int $id): ?array
    {
        $ledger = $this->fetch(
            'SELECT `id`, `amount` FROM `ledgers` 
                WHERE `id` = ? AND `canceled` = false AND `closed` = false
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
     * @return array<mixed>|null
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

    public function createAssignment(int $ledgerId, int $invoiceId): void
    {
        $this->execute(
            'INSERT INTO `ledgers_x_invoices` (`ledger_id`, `invoice_id`) VALUES (?, ?)',
            [$ledgerId, $invoiceId]
        );
    }

    public function markLedgerClosed(int $ledgerId): void
    {
        $this->execute('UPDATE `ledgers` SET `closed` = 1 WHERE `id` = ?', [$ledgerId]);
    }

    public function markInvoiceClosed(int $invoiceId): void
    {
        $this->execute('UPDATE `invoices` SET `closed` = 1 WHERE `id` = ?', [$invoiceId]);
    }

    public function fetchAssignedInvoicesForLedger(int $id): Generator
    {
        return $this->fetch(
            'SELECT i.`id`, i.`description` FROM `invoices` i 
                INNER JOIN `ledgers_x_invoices` x ON i.`id` = x.`invoice_id` WHERE x.`ledger_id` = ?',
            [$id]
        );
    }

    public function fetchAssignedLedgersForInvoice(int $id): Generator
    {
        return $this->fetch(
            'SELECT l.`id`, l.`description` FROM `ledgers` l 
                INNER JOIN `ledgers_x_invoices` x ON l.`id` = x.`ledger_id` WHERE x.`invoice_id` = ?',
            [$id]
        );
    }
}
