<?php

namespace App\Storage;

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
}
