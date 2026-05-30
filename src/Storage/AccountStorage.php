<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class AccountStorage extends Storage
{
    /**
     * Fetches all accounts ordered by account number.
     *
     * @return Generator<int, array<string, mixed>> Yields account data arrays.
     */
    public function fetchAll(): Generator
    {
        return $this->fetch('SELECT * FROM `accounts` ORDER BY `no`');
    }

    /**
     * Fetches only "real" (bank/cash) accounts ordered by account number.
     *
     * @return Generator<int, array<string, mixed>> Yields account data arrays.
     */
    public function fetchReal(): Generator
    {
        return $this->fetch('SELECT * FROM `accounts` WHERE `real` = true ORDER BY `no`');
    }

    /**
     * Fetches a single account by its account number.
     *
     * @param string $no The account number.
     *
     * @return array<string, mixed>|null The account data or null if not found.
     */
    public function fetchOne(string $no): ?array
    {
        $account = $this->fetch(
            'SELECT * FROM `accounts` WHERE `no` = ?',
            [$no]
        )->current();
        if ($account) {
            assert(is_array($account));
            return $account;
        }
        return null;
    }

    /**
     * Fetches a single "real" account by its account number.
     *
     * @param string $no The account number.
     *
     * @return array<string, mixed>|null The account data or null if not found.
     */
    public function fetchOneReal(string $no): ?array
    {
        $account = $this->fetch(
            'SELECT * FROM `accounts` WHERE `real` = true AND `no` = ?',
            [$no]
        )->current();
        if ($account) {
            assert(is_array($account));
            return $account;
        }
        return null;
    }

    /**
     * Inserts or updates an account.
     * Use this for synchronizing account definitions.
     *
     * @param string   $no         The account number.
     * @param string   $name       The account name.
     * @param int|null $categoryId The category ID.
     * @param bool     $real       Whether it's a "real" account.
     */
    public function upsert(string $no, string $name, ?int $categoryId, bool $real): void
    {
        $this->execute(
            'INSERT INTO `accounts` (`no`, `name`, `category_id`, `real`) VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE `name` = ?, `category_id` = ?, `real` = ?',
            [$no, $name, $categoryId, $real, $name, $categoryId, $real]
        );
    }

    /**
     * Removes all accounts that are NOT in the provided list of account numbers.
     *
     * @param list<string> $nos List of account numbers to keep.
     */
    public function removeAllWithout(array $nos): void
    {
        $this->execute(
            sprintf(
                'DELETE FROM `accounts` WHERE `no` NOT IN (%s)',
                implode(',', array_fill(0, count($nos), '?')),
            ),
            [...$nos]
        );
    }
}
