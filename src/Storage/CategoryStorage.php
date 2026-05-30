<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class CategoryStorage extends Storage
{
    /**
     * Fetches all categories ordered by ID.
     *
     * @return Generator<int, array<string, int|string>> Yields category data arrays.
     */
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT * FROM `categories` ORDER BY `id`');
    }

    /**
     * Inserts or updates a category.
     *
     * @param int    $id   The category ID.
     * @param string $name The category name.
     */
    public function upsert(int $id, string $name): void
    {
        $this->execute(
            'INSERT INTO `categories` (`id`, `name`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `name` = ?',
            [$id, $name, $name]
        );
    }

    /**
     * Removes all categories with an ID greater than the provided value.
     *
     * @param int $id The threshold ID.
     */
    public function removeAllAbove(int $id): void
    {
        $this->execute('DELETE FROM `categories` WHERE `id` > ?', [$id]);
    }

    /**
     * Fetches a single category by its ID.
     *
     * @param int $id The category ID.
     *
     * @return array<string, int|string>|null The category data or null if not found.
     */
    public function fetchOne(int $id): ?array
    {
        $category = $this->fetch('SELECT * FROM `categories` WHERE `id` = ?', [$id])->current();
        if ($category) {
            assert(is_array($category));
            return $category;
        }
        return null;
    }
}
