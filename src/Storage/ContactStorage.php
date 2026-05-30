<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class ContactStorage extends Storage
{
    /**
     * Fetches all contacts ordered by ID descending.
     *
     * @return Generator<int, array<string, int|string>> Yields contact data arrays.
     */
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT `id`, `name`, `mail`, `phone` FROM `contacts` ORDER BY `id` DESC');
    }

    /**
     * Fetches contacts matching a search term in name, mail, or phone.
     *
     * @param string $search The search term.
     *
     * @return Generator<int, array<string, int|string>> Yields matching contact data arrays.
     */
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
     * Fetches a single contact by ID, including aggregated income and expense sums.
     *
     * @param int $id The contact ID.
     *
     * @return array<string, int|string|null|float>|null The contact data or null if not found.
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

    /**
     * Removes a contact by ID.
     *
     * @param int $id The contact ID.
     */
    public function remove(int $id): void
    {
        $this->execute('DELETE FROM `contacts` WHERE `id` = ?', [$id]);
    }

    /**
     * Updates an existing contact.
     *
     * @param int    $id      The contact ID.
     * @param string $name    The contact name.
     * @param string $mail    The contact email.
     * @param string $phone   The contact phone number.
     * @param string $address The contact address.
     */
    public function update(int $id, string $name, string $mail, string $phone, string $address): void
    {
        $this->execute(
            'UPDATE `contacts` SET `name` = ?, `mail` = ?, `phone` = ?, `address` = ? WHERE `id` = ?',
            [$name, $mail ?: null, $phone ?: null, $address ?: null, $id]
        );
    }

    /**
     * Creates a new contact.
     *
     * @param string $name    The contact name.
     * @param string $mail    The contact email.
     * @param string $phone   The contact phone number.
     * @param string $address The contact address.
     */
    public function create(string $name, string $mail, string $phone, string $address): void
    {
        $this->execute(
            'INSERT INTO `contacts` (`name`, `mail`, `phone`, `address`) VALUES (?, ?, ?, ?)',
            [$name, $mail ?: null, $phone ?: null, $address ?: null]
        );
    }
}
