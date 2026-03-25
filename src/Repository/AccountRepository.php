<?php

namespace App\Repository;

use App\Storage\AccountStorage;

class AccountRepository
{
    public function __construct(
        private readonly AccountStorage $storage,
    ) {
    }

    /**
     * @return list<mixed>
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
     * @param list<string> $nos
     * @param list<string> $names
     * @param list<string> $categories
     * @param list<string> $reals
     */
    public function updateAccounts(array $nos, array $names, array $categories, array $reals): void
    {
        foreach ($nos as $index => $no) {
            $name = $names[$index] ?? '';
            $categoryId = empty($categories[$index]) ? null : (int) $categories[$index];
            $real = in_array($no, $reals);
            $this->storage->upsert($no, $name, $categoryId, $real);
        }
        $this->storage->removeAllWithout($nos);
    }
}
