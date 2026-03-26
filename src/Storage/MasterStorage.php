<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class MasterStorage extends Storage
{
    public function fetchAllValues(): Generator
    {
        return $this->fetch('SELECT `key`, `value` FROM `master` WHERE `value` IS NOT NULL');
    }

    public function upsert(string $key, string $value): void
    {
        $this->execute(
            'INSERT INTO `master` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?',
            [$key, $value, $value ?: null]
        );
    }
}
