<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class LedgerStorage extends Storage
{
    public function sumRealAccounts(): Generator
    {
        return $this->fetch(
            'SELECT `account_no`, SUM(`amount`) AS `sum` FROM `ledgers` 
            WHERE `canceled` = false GROUP BY `account_no`'
        );
    }

    public function sumCategories(): Generator
    {
        return $this->fetch(
            'SELECT c.`id`, c.`name`, SUM(l.`amount`) AS `sum` FROM `ledgers` l
            LEFT JOIN `accounts` a ON a.`no` = l.`offset_no` 
            LEFT JOIN `categories` c ON a.`category_id` = c.`id`
            WHERE l.`canceled` = false GROUP BY c.`id`'
        );
    }

    public function countUnassigned(): int
    {
        return $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `ledgers` WHERE `closed` = false'
        )->current()['count'] ?: 0;
    }
}
