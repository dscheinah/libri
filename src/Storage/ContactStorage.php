<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class ContactStorage extends Storage
{
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT `id`, `name`, `mail`, `phone` FROM `contacts` ORDER BY `id` DESC');
    }

    public function fetchSome(string $search): Generator
    {
        $search = '%' . $search . '%';
        return $this->fetch(
            'SELECT `id`, `name`, `mail`, `phone` FROM `contacts` 
                WHERE `name` LIKE ? OR `mail` LIKE ? OR `phone` LIKE ? 
                ORDER BY `id` DESC',
            [$search, $search, $search]
        );
    }

    /**
     * @return array<string, string|null|float>|null
     */
    public function fetchOne(int $id): ?array
    {
        $contact = $this->fetch(
            'SELECT c.*, 
                    SUM(IF(i.`amount` > 0, i.`amount`, 0)) AS `income`, 
                    SUM(IF(i.`amount` < 0, i.`amount`, 0)) AS `expense` 
                FROM `contacts` AS c LEFT JOIN `invoices` i ON i.`contact_id` = c.`id`
                WHERE c.`id` = ?
                GROUP BY c.`id`',
            [$id]
        )->current();
        if ($contact) {
            assert(is_array($contact));
            return $contact;
        }
        return null;
    }

    public function remove(int $id): void
    {
        $this->execute('DELETE FROM `contacts` WHERE `id` = ?', [$id]);
    }

    public function update(int $id, string $name, string $mail, string $phone, string $address): void
    {
        $this->execute(
            'UPDATE `contacts` SET `name` = ?, `mail` = ?, `phone` = ?, `address` = ? WHERE `id` = ?',
            [$name, $mail ?: null, $phone ?: null, $address ?: null, $id]
        );
    }

    public function create(string $name, string $mail, string $phone, string $address): void
    {
        $this->execute(
            'INSERT INTO `contacts` (`name`, `mail`, `phone`, `address`) VALUES (?, ?, ?, ?)',
            [$name, $mail ?: null, $phone ?: null, $address ?: null]
        );
    }
}
