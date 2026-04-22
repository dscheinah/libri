<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class LedgerStorage extends Storage
{
    public function sumRealAccounts(): Generator
    {
        return $this->fetch(
            'SELECT `account_no`, SUM(`amount`) AS `sum` FROM `ledgers` 
            WHERE `canceled` = false GROUP BY `account_no`'
        );
    }

    public function sumCategories(): Generator
    {
        return $this->fetch(
            'SELECT c.`id`, c.`name`, SUM(l.`amount`) AS `sum` FROM `ledgers` l
            LEFT JOIN `accounts` a ON a.`no` = l.`offset_no` 
            LEFT JOIN `categories` c ON a.`category_id` = c.`id`
            WHERE l.`canceled` = false GROUP BY c.`id`'
        );
    }

    public function countUnassigned(): int
    {
        $result = $this->fetch(
            'SELECT COUNT(*) AS `count` FROM `ledgers` WHERE `closed` = false'
        )->current();
        if ($result) {
            assert(is_array($result));
            return $result['count'] ?? 0;
        }
        return 0;
    }

    public function fetchSome(string $account, string $search): Generator
    {
        $account = '%' . $account . '%';
        $search = '%' . $search . '%';
        return $this->fetch(
            'SELECT 
                    l.`id`, 
                    l.`date`,
                    l.`account_no`, 
                    a.`name` AS `account_name`,
                    l.`offset_no`, 
                    o.name AS `offset_name`,
                    l.`description`, 
                    l.`amount`,
                    l.`closed`, 
                    l.`reference`, 
                    l.`canceled` 
                FROM `ledgers` l
                    LEFT JOIN `accounts` a ON a.`no` = l.`account_no`
                    LEFT JOIN `accounts` o ON o.`no` = l.`offset_no`
                WHERE `account_no` LIKE ? AND (`id` LIKE ? OR `description` LIKE ? OR `reference` LIKE ?) 
                ORDER BY `id` DESC',
            [$account, $search, $search, $search]
        );
    }

    public function fetchOpen(): Generator
    {
        return $this->fetch(
            'SELECT `id`, `date`, `description`, `amount`, `reference` FROM `ledgers` 
                WHERE `closed` = false AND `canceled` = false'
        );
    }

    /**
     * @return array<string, int|string|null|float>|null
     */
    public function fetchOne(int $id): ?array
    {
        $ledger = $this->fetch(
            'SELECT l.*, a.`name` AS `account_name`, o.name AS `offset_name` 
                FROM `ledgers` l
                    LEFT JOIN `accounts` a ON a.`no` = l.`account_no`
                    LEFT JOIN `accounts` o ON o.`no` = l.`offset_no`
                WHERE `id` = ?',
            [$id]
        )->current();
        if ($ledger) {
            assert(is_array($ledger));
            return $ledger;
        }
        return null;
    }

    public function updateCanceled(int $id, string $reason): void
    {
        $this->execute('UPDATE `ledgers` SET `canceled` = true, `canceled_reason` = ? WHERE `id` = ?', [$reason, $id]);
    }

    public function create(
        string $date,
        string $accountNo,
        string $offsetNo,
        float $amount,
        string $description,
        string $reference,
    ): void {
        $latest = $this->fetch('SELECT MAX(`id`) AS `id` FROM `ledgers` FOR UPDATE')->current();
        if ($latest) {
            assert(is_array($latest));
            $id = $latest['id'];
        } else {
            $id = 0;
        }
        $this->execute(
            'INSERT INTO `ledgers` (`id`, `date`, `account_no`, `offset_no`, `amount`, `description`, `reference`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$id + 1, $date, $accountNo, $offsetNo, $amount, $description, $reference]
        );
    }
}
