<?php

namespace App\Repository;

use App\Storage\MasterStorage;

class MasterRepository
{
    public function __construct(
        private readonly MasterStorage $storage,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function loadEntries(): array
    {
        return array_column(iterator_to_array($this->storage->fetchAllValues()), 'value', 'key');
    }

    /**
     * @param array<string, scalar|array<mixed>> $data
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
