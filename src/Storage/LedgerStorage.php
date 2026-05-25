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
            'SELECT COUNT(*) AS `count` FROM `ledgers` WHERE `canceled` = false AND  `closed` = false AND `transfer` = false'
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
                    l.`canceled`,
                    l.`transfer`
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
                WHERE `closed` = false AND `canceled` = false AND `transfer` = false'
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
        $this->execute('UPDATE `ledgers` SET `canceled` = true, `canceled_reason` = ? WHERE `id` = ? AND `closed` = false', [$reason, $id]);
    }

    public function create(
        string $date,
        string $accountNo,
        string $offsetNo,
        float $amount,
        string $description,
        string $reference,
    ): int {
        $id = $this->getNextInsertId();
        $this->execute(
            'INSERT INTO `ledgers` (`id`, `date`, `account_no`, `offset_no`, `amount`, `description`, `reference`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$id, $date, $accountNo, $offsetNo, $amount, $description, $reference]
        );
        return $id;
    }

    public function createTransfer(
        string $date,
        string $accountNo,
        string $offsetNo,
        float $amount,
        string $description,
        string $reference,
    ): int {
        $id = $this->getNextInsertId();
        $this->execute(
            'INSERT INTO `ledgers` (`id`, `date`, `account_no`, `offset_no`, `amount`, `description`, `reference`, `transfer`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, true)',
            [$id, $date, $accountNo, $offsetNo, $amount, $description, $reference]
        );
        return $id;
    }

    private function getNextInsertId(): int
    {
        $latest = $this->fetch('SELECT MAX(`id`) AS `id` FROM `ledgers` FOR UPDATE')->current();
        if ($latest) {
            assert(is_array($latest));
            return $latest['id'] + 1;
        }
        return 1;
    }

    public function fetchAmountBeforeDateByAccount(string $account, string $date): float
    {
        $amount = $this->fetch(
            'SELECT SUM(`amount`)
                FROM `ledgers`
                WHERE (`account_no` = ? OR `offset_no` = ?) AND `date` < ? AND `canceled` = false',
            [$account, $account, $date]
        )->current();
        assert(is_array($amount));
        return $amount['amount'] ?? 0;
    }

    public function fetchAmountAtDateByAccount(string $account, string $date): float
    {
        $amount = $this->fetch(
            'SELECT SUM(`amount`) AS `amount`
                FROM `ledgers`
                WHERE (`account_no` = ? OR `offset_no` = ?) AND `date` <= ? AND `canceled` = false',
            [$account, $account, $date]
        )->current();
        assert(is_array($amount));
        return $amount['amount'] ?? 0;
    }

    /**
     * @return array<string, float|null>
     */
    public function fetchSummaryByAccount(string $account, string $start, string $end): array
    {
        $summary = $this->fetch(
            'SELECT 
                    SUM(IF(`amount` > 0, `amount`, 0)) AS `income`,
                    SUM(IF(`amount` < 0, `amount`, 0)) AS `expense`,
                    SUM(`amount`) AS `total`
                FROM `ledgers` 
                WHERE (`account_no` = ? OR `offset_no` = ?) AND `date` BETWEEN ? AND ? AND `closed` = true AND `canceled` = false',
            [$account, $account, $start, $end],
        )->current();
        assert(is_array($summary));
        return $summary;
    }

    public function fetchForReportByAccount(string $account, string $start, string $end): Generator
    {
        return $this->fetch(
            'SELECT 
                    `id`,
                    `date`,
                    `offset_no`,
                    `description`,
                    `amount`,
                    `reference`
                FROM `ledgers`
                WHERE (`account_no` = ? OR `offset_no` = ?) AND `date` BETWEEN ? AND ? AND `closed` = true AND `canceled` = false',
            [$account, $account, $start, $end]
        );
    }

    public function fetchAmountBeforeDateByCategory(int $category, string $date): float
    {
        $amount = $this->fetch(
            'SELECT SUM(l.`amount`) AS `amount` 
                FROM `ledgers` l INNER JOIN `accounts` a ON a.`no` = l.`offset_no` 
                WHERE a.`category_id` = ? AND `date` < ? AND `canceled` = false',
            [$category, $date]
        )->current();
        assert(is_array($amount));
        return $amount['amount'] ?? 0;
    }

    public function fetchAmountAtDateByCategory(int $category, string $date): float
    {
        $amount = $this->fetch(
            'SELECT SUM(l.`amount`) AS `amount`
                FROM `ledgers` l INNER JOIN `accounts` a ON a.`no` = l.`offset_no`
                WHERE a.`category_id` = ? AND `date` <= ? AND `canceled` = false',
            [$category, $date]
        )->current();
        assert(is_array($amount));
        return $amount['amount'] ?? 0;
    }

    /**
     * @return array<string, float|null>
     */
    public function fetchSummaryByCategory(int $category, string $start, string $end): array
    {
        $summary = $this->fetch(
            'SELECT 
                    SUM(IF(l.`amount` > 0, l.`amount`, 0)) AS `income`,
                    SUM(IF(l.`amount` < 0, l.`amount`, 0)) AS `expense`,
                    SUM(l.`amount`) AS `total`
                FROM `ledgers` l INNER JOIN `accounts` a ON a.`no` = l.`offset_no`
                WHERE a.`category_id` = ? AND `date` BETWEEN ? AND ? AND `closed` = true AND `canceled` = false',
            [$category, $start, $end],
        )->current();
        assert(is_array($summary));
        return $summary;
    }

    public function fetchForReportByCategory(int $category, string $start, string $end): Generator
    {
        return $this->fetch(
            'SELECT 
                    l.`id`,
                    l.`date`,
                    l.`offset_no`,
                    l.`description`,
                    l.`amount`,
                    l.`reference`
                FROM `ledgers` l INNER JOIN `accounts` a ON a.`no` = l.`offset_no`
                WHERE a.`category_id` = ? AND `date` BETWEEN ? AND ? AND `closed` = true AND `canceled` = false',
            [$category, $start, $end]
        );
    }

    public function fetchCanceled(string $start, string $end): Generator
    {
        return $this->fetch(
            'SELECT 
                    `id`,
                    `date`,
                    `account_no`,
                    `amount`,
                    `canceled_reason` 
                FROM `ledgers`
                WHERE `date` BETWEEN ? AND ? AND `canceled` = true',
            [$start, $end]
        );
    }

    public function fetchUnassigned(): Generator
    {
        return $this->fetch(
            'SELECT 
                    `id`,
                    `date`,
                    `account_no`,
                    `description`,
                    `amount`,
                    `reference`
                FROM `ledgers`
                WHERE `canceled` = false AND  `closed` = false AND `transfer` = false',
        );
    }
}
