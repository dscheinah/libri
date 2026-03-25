<?php

namespace App\Repository;

use App\Storage\CategoryStorage;

class CategoryRepository
{
    public function __construct(
        private readonly CategoryStorage $storage,
    ) {
    }

    /**
     * @return list<mixed>
     */
    public function listCategories(): array
    {
        return iterator_to_array($this->storage->fetchAll());
    }

    /**
     * @param list<string> $names
     */
    public function updateCategories(array $names): void
    {
        foreach ($names as $index => $name) {
            $this->storage->upsert($index + 1, $name);
        }
        $this->storage->removeAllAbove(count($names));
    }
}
