<?php

namespace App\Repository;

use App\Storage\LedgerStorage;

class LedgerRepository
{
    public function __construct(
        private readonly LedgerStorage $storage,
    ) {
    }

    /**
     * @return list<mixed>
     */
    public function listLedgers(string $account, string $search): array
    {
        $ledgers = [];
        foreach ($this->storage->fetchSome($account, $search) as $ledger) {
            assert(is_array($ledger));
            $ledgers[] = [
                'id' => (int) $ledger['id'],
                'date' => $ledger['date'],
                'account' => [
                    'no' => $ledger['account_no'],
                    'description' => implode(' - ', array_filter([$ledger['account_no'], $ledger['account_name']])),
                ],
                'offset' => [
                    'no' => $ledger['offset_no'],
                    'description' => implode(' - ', array_filter([$ledger['offset_no'], $ledger['offset_name']])),
                ],
                'description' => $ledger['description'],
                'amount' => (float) $ledger['amount'],
                'assigned' => (bool) $ledger['closed'],
                'reference' => $ledger['reference'],
                'canceled' => (bool) $ledger['canceled'],
            ];
        }
        return $ledgers;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getLedger(int $id): ?array
    {
        $ledger = $this->storage->fetchOne($id);
        if (!$ledger) {
            return null;
        }
        return [
            'id' => (int) $ledger['id'],
            'date' => $ledger['date'],
            'account' => [
                'no' => $ledger['account_no'],
                'description' => implode(' - ', array_filter([$ledger['account_no'], $ledger['account_name']])),
            ],
            'offset' => [
                'no' => $ledger['offset_no'],
                'description' => implode(' - ', array_filter([$ledger['offset_no'], $ledger['offset_name']])),
            ],
            'description' => $ledger['description'],
            'amount' => (float) $ledger['amount'],
            'assigned' => (bool) $ledger['closed'],
            'invoices' => [],
            'reference' => $ledger['reference'],
            'canceled' => (bool) $ledger['canceled'],
        ];
    }

    public function cancelLedger(int $id, string $reason): void
    {
        $this->storage->updateCanceled($id, $reason);
    }

    /**
     * @param array<string> $dates
     * @param array<string> $accounts
     * @param array<string> $offsets
     * @param array<string> $amounts
     * @param array<string> $descriptions
     * @param array<string> $references
     */
    public function createLedgers(
        int $count,
        array $dates,
        array $accounts,
        array $offsets,
        array $amounts,
        array $descriptions,
        array $references
    ): void {
        $this->storage->transactional(
            function () use ($count, $dates, $accounts, $offsets, $amounts, $descriptions, $references) {
                for ($index = 0; $index < $count; $index++) {
                    $this->storage->create(
                        $dates[$index],
                        $accounts[$index],
                        $offsets[$index],
                        (float) $amounts[$index],
                        $descriptions[$index],
                        $references[$index],
                    );
                }
                return true;
            }
        );
    }
}
