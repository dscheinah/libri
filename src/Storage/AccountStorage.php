<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class AccountStorage extends Storage
{
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT * FROM `accounts` ORDER BY `no`');
    }

    public function fetchReal(): Generator
    {
        return $this->fetch('SELECT * FROM `accounts` WHERE `real` = true ORDER BY `no`');
    }

    public function upsert(string $no, string $name, ?int $categoryId, bool $real): void
    {
        $this->execute(
            'INSERT INTO `accounts` (`no`, `name`, `category_id`, `real`) VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE `name` = ?, `category_id` = ?, `real` = ?',
            [$no, $name, $categoryId, $real, $name, $categoryId, $real]
        );
    }

    /**
     * @param list<string> $nos
     */
    public function removeAllWithout(array $nos): void
    {
        $this->execute(
            sprintf(
                'DELETE FROM `accounts` WHERE `no` NOT IN (%s)',
                implode(',', array_fill(0, count($nos), '?')),
            ),
            [...$nos]
        );
    }
}
