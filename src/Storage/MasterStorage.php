<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class MasterStorage extends Storage
{
    /**
     * Fetches all master data keys and values that are not null.
     *
     * @return Generator<int, array<string, string>> Yields key-value pairs.
     */
    public function fetchAllValues(): Generator
    {
        return $this->fetch('SELECT `key`, `value` FROM `master` WHERE `value` IS NOT NULL');
    }

    /**
     * Inserts or updates a master data entry.
     *
     * @param string $key   The setting key.
     * @param string $value The setting value.
     */
    public function upsert(string $key, string $value): void
    {
        $this->execute(
            'INSERT INTO `master` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?',
            [$key, $value, $value ?: null]
        );
    }
}
