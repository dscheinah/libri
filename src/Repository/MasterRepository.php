<?php

namespace App\Repository;

use App\Storage\MasterStorage;

/**
 * Use this repository to handle master data (configuration/settings like company address, bank account).
 */
class MasterRepository
{
    public function __construct(
        private readonly MasterStorage $storage,
    ) {
    }

    /**
     * Loads all master data entries as an associative array.
     * Use this method to retrieve settings for the settings page or for document generation.
     *
     * @return array<int|string, string> Key-value pairs of settings.
     */
    public function loadEntries(): array
    {
        return array_column(iterator_to_array($this->storage->fetchAllValues()), 'value', 'key');
    }

    /**
     * Stores master data entries.
     * Use this method to save changes from the master data settings page.
     *
     * @param array<string, scalar|array<mixed>> $data Associative array of settings to store.
     */
    public function storeEntries(array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $this->storage->upsert($key, (string) $value);
        }
    }
}
