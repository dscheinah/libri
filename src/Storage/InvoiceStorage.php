<?php

namespace App\Storage;

use Sx\Data\Storage;

class InvoiceStorage extends Storage
{
    public function countUnassigned(): int
    {
        return $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `invoices` WHERE `closed` = false'
        )->current()['count'] ?? 0;
    }

    public function countWithoutDocument(): int
    {
        return $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `invoices`
            WHERE `document` IS NULL AND `no_document` = false AND `finished` = false'
        )->current()['count'] ?: 0;
    }
}
