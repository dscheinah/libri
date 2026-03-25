<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class CategoryStorage extends Storage
{
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT * FROM `categories` ORDER BY `id`');
    }

    public function upsert(int $id, string $name): void
    {
        $this->execute(
            'INSERT INTO `categories` (`id`, `name`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `name` = ?',
            [$id, $name, $name]
        );
    }

    public function removeAllAbove(int $id): void
    {
        $this->execute('DELETE FROM `categories` WHERE `id` > ?', [$id]);
    }
}
