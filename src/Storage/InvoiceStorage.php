<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class InvoiceStorage extends Storage
{
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

    public function fetchAll(int $type): Generator
    {
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `closed`, `reference`, `no_document`, `finished` 
                FROM `invoices` 
                WHERE `type` = ? 
                ORDER BY `id` DESC',
            [$type]
        );
    }

    public function fetchSome(int $type, string $search): Generator
    {
        $search = '%' . $search . '%';
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `closed`, `reference`, `no_document`, `finished` 
                FROM `invoices` 
                WHERE `type` = ? AND (`id` LIKE ? OR `description` LIKE ? OR `reference` LIKE ?) 
                ORDER BY `id` DESC',
            [$type, $search, $search, $search]
        );
    }

    public function fetchOpen(): Generator
    {
        return $this->fetch(
            'SELECT `id`, `type`, `date`, `description`, `amount`, `reference` FROM `invoices` 
                WHERE `closed` = false AND (`type` = 1 OR `finished` = true)'
        );
    }

    /**
     * @return array<string, int|string|null|float>|null
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

    public function removeOpenInvoice(int $id): int
    {
        return $this->execute(
            'DELETE FROM `invoices` WHERE `id` = ? AND `closed` = false AND `finished` = false',
            [$id]
        );
    }

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

    public function updateAll(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $reference,
        bool $noDocument,
        string $contactAddress,
    ): void {
        $this->execute(
            'UPDATE `invoices` SET 
                `date` = ?, `amount` = ?, `description` = ?, `reference` = ?, `no_document` = ?, `contact_address` = ?
                WHERE `id` = ?',
            [$date, $amount, $description, $reference, $noDocument, $contactAddress, $id]
        );
    }

    public function updateDetails(
        int $id,
        string $description,
        string $reference,
        bool $noDocument,
    ): void {
        $this->execute(
            'UPDATE `invoices` SET 
                `description` = ?, `reference` = ?, `no_document` = ?
                WHERE `id` = ?',
            [$description, $reference, $noDocument, $id]
        );
    }

    public function updateDocument(int $id, string $name, string $content): void
    {
        $this->execute(
            'UPDATE `invoices` SET `document_name` = ?, `document` = ? WHERE `id` = ?',
            [$name, $content, $id]
        );
    }
}
