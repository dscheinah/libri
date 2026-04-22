<?php

namespace App\Repository;

use App\Storage\AssignmentStorage;
use App\Storage\LedgerStorage;
use DomainException;

class LedgerRepository
{
    public function __construct(
        private readonly LedgerStorage $storage,
        private readonly AssignmentStorage $assignmentStorage,
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
     * @return list<mixed>
     */
    public function listOpenLedgers(): array
    {
        $ledgers = [];
        foreach ($this->storage->fetchOpen() as $ledger) {
            assert(is_array($ledger));
            $ledgers[] = [
                'id' => (int) $ledger['id'],
                'date' => $ledger['date'],
                'description' => $ledger['description'],
                'amount' => (float) $ledger['amount'],
                'reference' => $ledger['reference'],
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
            'invoices' => iterator_to_array($this->assignmentStorage->fetchAssignedInvoicesForLedger($id)),
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

    /**
     * @param list<int> $invoiceIds
     */
    public function assignInvoices(int $ledgerId, array $invoiceIds): void
    {
        $this->assignmentStorage->transactional(function () use ($ledgerId, $invoiceIds) {
            $ledger = $this->assignmentStorage->fetchOpenLedger($ledgerId);
            if (!$ledger) {
                throw new DomainException('ledger does not exist', 400);
            }
            $amount = 0.0;
            foreach ($invoiceIds as $invoiceId) {
                $invoice = $this->assignmentStorage->fetchOpenInvoice($invoiceId);
                if (!$invoice) {
                    throw new DomainException('invoice does not exist', 400);
                }
                $this->assignmentStorage->createAssignment($ledgerId, $invoiceId);
                $this->assignmentStorage->markInvoiceClosed($invoiceId);
                $amount += $invoice['amount'];
            }
            assert(is_numeric($ledger['amount']));
            if ((float) $ledger['amount'] !== $amount) {
                throw new DomainException('amounts do not match', 400);
            }
            $this->assignmentStorage->markLedgerClosed($ledgerId);
            return true;
        });
    }
}
