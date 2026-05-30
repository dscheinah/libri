<?php

namespace App\Repository;

use App\Storage\CategoryStorage;

/**
 * Use this repository to handle category management.
 */
class CategoryRepository
{
    public function __construct(
        private readonly CategoryStorage $storage,
    ) {
    }

    /**
     * Retrieves all categories.
     * Use this method to get a list of all available categories for dropdowns or settings.
     *
     * @return list<mixed> A list of category data.
     */
    public function listCategories(): array
    {
        return iterator_to_array($this->storage->fetchAll());
    }

    /**
     * Updates the categories in the storage.
     * Categories are indexed by their position in the provided $names list (starting from 1).
     * Categories with higher IDs than the count of provided names will be removed.
     * Use this method to save changes from a category management interface.
     *
     * @param list<string> $names List of category names.
     */
    public function updateCategories(array $names): void
    {
        foreach ($names as $index => $name) {
            $this->storage->upsert($index + 1, $name);
        }
        $this->storage->removeAllAbove(count($names));
    }
}
