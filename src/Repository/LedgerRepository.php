<?php

namespace App\Repository;

use App\Storage\AccountStorage;
use App\Storage\AssignmentStorage;
use App\Storage\LedgerStorage;
use DomainException;

/**
 * Use this repository to handle ledger entries (journal), cancellations, and assignments to invoices.
 */
class LedgerRepository
{
    public function __construct(
        private readonly LedgerStorage $storage,
        private readonly AssignmentStorage $assignmentStorage,
        private readonly AccountStorage $accountStorage,
    ) {
    }

    /**
     * Retrieves a list of ledger entries based on account and optional search term.
     * Use this method to display the journal for a specific account or filtered by text.
     *
     * @param string $account The account number to filter by.
     * @param string $search  Optional search term to filter results.
     *
     * @return list<mixed> A list of ledger data arrays.
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
                'transfer' => (bool) $ledger['transfer'],
            ];
        }
        return $ledgers;
    }

    /**
     * Retrieves a list of open (unassigned) ledger entries.
     * Use this method for selecting ledgers that need to be assigned to invoices.
     *
     * @return list<mixed> A list of open ledger entries.
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
     * Retrieves a single ledger entry by its ID, including assigned invoices.
     * Use this method for viewing or editing ledger entry details.
     *
     * @param int $id The unique identifier of the ledger entry.
     *
     * @return array<string, mixed>|null The ledger data or null if not found.
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
            'transfer' => (bool) $ledger['transfer'],
        ];
    }

    /**
     * Cancels a ledger entry with a reason.
     * Use this method to void an entry without deleting it from the journal.
     *
     * @param int    $id     The ID of the ledger entry to cancel.
     * @param string $reason The reason for cancellation.
     */
    public function cancelLedger(int $id, string $reason): void
    {
        $this->storage->updateCanceled($id, $reason);
    }

    /**
     * Creates multiple ledger entries in a single transaction.
     * Automatically handles transfers if the offset account is marked as "real".
     * Use this method for bulk importing or entering multiple journal entries.
     *
     * @param int           $count        Number of entries to create.
     * @param array<string> $dates        List of dates.
     * @param array<string> $accounts     List of account numbers.
     * @param array<string> $offsets      List of offset account numbers.
     * @param array<string> $amounts      List of amounts as strings.
     * @param array<string> $descriptions List of descriptions.
     * @param array<string> $references   List of references.
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
                    if ($this->accountStorage->fetchOneReal($offsets[$index])) {
                        $this->storage->createTransfer(
                            $dates[$index],
                            $offsets[$index],
                            $accounts[$index],
                            -1.0 * (float) $amounts[$index],
                            $descriptions[$index],
                            $references[$index],
                        );
                    }
                }
                return true;
            }
        );
    }

    /**
     * Assigns one or more invoices to a ledger entry.
     * Ensures that the total amount of invoices matches the ledger amount.
     * Both invoices and ledger entry are marked as closed upon successful assignment.
     *
     * @param int       $ledgerId   The ID of the ledger entry.
     * @param list<int> $invoiceIds List of invoice IDs to assign.
     *
     * @throws DomainException If the ledger or an invoice does not exist, or if amounts don't match.
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
