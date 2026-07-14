<?php

namespace App\Storage;

use Generator;
use Sx\Data\Storage;

class LedgerStorage extends Storage
{
    /**
     * Calculates the sum of balances for all "real" accounts.
     *
     * @return Generator<int, array<string, string|float>> Yields sums per account.
     */
    public function sumRealAccounts(): Generator
    {
        return $this->fetch(
            'SELECT `account_no`, SUM(`amount`) AS `sum` FROM `ledgers` 
            WHERE `canceled` = false GROUP BY `account_no`'
        );
    }

    /**
     * Calculates the sum of ledger entries grouped by category.
     *
     * @return Generator<int, array<string, int|string|float>> Yields sums per category.
     */
    public function sumCategories(): Generator
    {
        return $this->fetch(
            'SELECT c.`id`, c.`name`, SUM(l.`amount`) AS `sum` FROM `ledgers` l
            LEFT JOIN `accounts` a ON a.`no` = l.`offset_no` 
            LEFT JOIN `categories` c ON a.`category_id` = c.`id`
            WHERE l.`canceled` = false GROUP BY c.`id`'
        );
    }

    /**
     * Counts all ledger entries that have not been assigned to any invoice.
     *
     * @return int The count of unassigned ledger entries.
     */
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

    /**
     * Fetches ledger entries filtered by account and search term.
     *
     * @param string $account Account number.
     * @param string $search  Search term.
     *
     * @return Generator<int, array<string, int|string|float>> Yields matching ledger entries.
     */
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
                ORDER BY `date` DESC',
            [$account, $search, $search, $search]
        );
    }

    /**
     * Fetches all ledger entries that are unassigned and not canceled or transfers.
     * Use this method to retrieve entries for assignment to invoices.
     *
     * @return Generator<int, array<string, int|string|float>> Yields open ledger data arrays.
     */
    public function fetchOpen(): Generator
    {
        return $this->fetch(
            'SELECT `id`, `date`, `description`, `amount`, `reference` FROM `ledgers` 
                WHERE `closed` = false AND `canceled` = false AND `transfer` = false'
        );
    }

    /**
     * Fetches a single ledger entry by ID.
     *
     * @param int $id The ledger entry ID.
     *
     * @return array<string, int|string|null|float>|null The ledger entry data or null if not found.
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

    /**
     * Marks a ledger entry as canceled with a reason.
     *
     * @param int    $id     The ledger entry ID.
     * @param string $reason The cancellation reason.
     */
    public function updateCanceled(int $id, string $reason): void
    {
        $this->execute('UPDATE `ledgers` SET `canceled` = true, `canceled_reason` = ? WHERE `id` = ? AND `closed` = false', [$reason, $id]);
    }

    /**
     * Creates a new ledger entry.
     *
     * @param string $date        Transaction date.
     * @param string $accountNo   Account number.
     * @param string $offsetNo    Offset account number.
     * @param float  $amount      Amount.
     * @param string $description Description.
     * @param string $reference   Reference.
     *
     * @return int The ID of the newly created entry.
     */
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

    /**
     * Creates a new ledger entry marked as a transfer.
     * Use this when moving funds between real accounts.
     *
     * @param string $date        Transaction date.
     * @param string $accountNo   Account number.
     * @param string $offsetNo    Offset account number.
     * @param float  $amount      Amount.
     * @param string $description Description.
     * @param string $reference   Reference.
     *
     * @return int The ID of the newly created transfer entry.
     */
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

    /**
     * Retrieves the next available ID for a new ledger entry.
     * Uses FOR UPDATE to prevent race conditions during bulk inserts.
     *
     * @return int The next ID.
     */
    private function getNextInsertId(): int
    {
        $latest = $this->fetch('SELECT MAX(`id`) AS `id` FROM `ledgers` FOR UPDATE')->current();
        if ($latest) {
            assert(is_array($latest));
            return $latest['id'] + 1;
        }
        return 1;
    }

    /**
     * Fetches the total balance of an account before a specific date.
     *
     * @param string $account Account number.
     * @param string $date    The threshold date (exclusive).
     *
     * @return float The balance sum.
     */
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

    /**
     * Fetches the total balance of an account up to and including a specific date.
     *
     * @param string $account Account number.
     * @param string $date    The threshold date (inclusive).
     *
     * @return float The balance sum.
     */
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
     * Calculates the balance summary (income, expense, total) for an account within a period.
     *
     * @param string $account Account number.
     * @param string $start   Start date.
     * @param string $end     End date.
     *
     * @return array<string, float|null> Summary data.
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

    /**
     * Fetches detailed ledger entries for an account within a date range for reporting.
     * Only includes closed (assigned) and non-canceled entries.
     *
     * @param string $account Account number.
     * @param string $start   Start date.
     * @param string $end     End date.
     *
     * @return Generator<int, array<string, int|string|float>> Yields ledger entries.
     */
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

    /**
     * Fetches the total balance for all accounts in a category before a specific date.
     *
     * @param int    $category Category ID.
     * @param string $date     The threshold date (exclusive).
     *
     * @return float The balance sum.
     */
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

    /**
     * Fetches the total balance for all accounts in a category up to and including a specific date.
     *
     * @param int    $category Category ID.
     * @param string $date     The threshold date (inclusive).
     *
     * @return float The balance sum.
     */
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
     * Calculates the balance summary (income, expense, total) for a category within a period.
     *
     * @param int    $category Category ID.
     * @param string $start    Start date.
     * @param string $end      End date.
     *
     * @return array<string, float|null> Summary data.
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

    /**
     * Fetches detailed ledger entries for a category within a date range for reporting.
     * Only includes closed (assigned) and non-canceled entries.
     *
     * @param int    $category Category ID.
     * @param string $start    Start date.
     * @param string $end      End date.
     *
     * @return Generator<int, array<string, int|string|float>> Yields ledger entries.
     */
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

    /**
     * Fetches all canceled ledger entries within a date range.
     *
     * @param string $start Start date.
     * @param string $end   End date.
     *
     * @return Generator<int, array<string, int|string|float>> Yields canceled ledger data arrays.
     */
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

    /**
     * Fetches all unassigned, non-canceled, non-transfer ledger entries.
     * Similar to fetchOpen() but provides more columns for detailed lists.
     *
     * @return Generator<int, array<string, int|string|float>> Yields ledger data arrays.
     */
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
