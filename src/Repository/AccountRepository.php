<?php

namespace App\Repository;

use App\Storage\AccountStorage;

/**
 * Use this repository to handle account-related business logic and data transformations.
 */
class AccountRepository
{
    public function __construct(
        private readonly AccountStorage $storage,
    ) {
    }

    /**
     * Retrieves a list of accounts.
     * Use this method to get all accounts or only "real" (bank/cash) accounts for selection lists or tables.
     *
     * @param bool $filterForReal If true, only returns accounts marked as 'real'.
     *
     * @return list<mixed> A list of account data arrays with keys: no, name, category, real.
     */
    public function listAccounts(bool $filterForReal): array
    {
        $accounts = [];
        foreach ($filterForReal ? $this->storage->fetchReal() : $this->storage->fetchAll() as $account) {
            assert(is_array($account));
            $accounts[] = [
                'no' => $account['no'],
                'name' => $account['name'],
                'category' => $account['category_id'] ?? null,
                'real' => (bool) $account['real'],
            ];
        }
        return $accounts;
    }

    /**
     * Updates the accounts in the storage by synchronizing with the provided data.
     * Existing accounts not present in the $nos list will be removed.
     * Use this method to save changes from an account management interface.
     *
     * @param list<string> $nos        List of account numbers.
     * @param list<string> $names      List of account names corresponding to the numbers.
     * @param list<string> $categories List of category IDs for the accounts.
     * @param list<string> $reals      List of account numbers that should be marked as "real".
     */
    public function updateAccounts(array $nos, array $names, array $categories, array $reals): void
    {
        foreach ($nos as $index => $no) {
            $name = $names[$index] ?? '';
            $real = in_array($no, $reals);
            $categoryId = $real || empty($categories[$index]) ? null : (int) $categories[$index];
            $this->storage->upsert($no, $name, $categoryId, $real);
        }
        $this->storage->removeAllWithout($nos);
    }
}
