<?php

namespace App\Repository;

use App\Storage\AssignmentStorage;
use App\Storage\InvoiceStorage;
use DomainException;

class InvoiceRepository
{
    public function __construct(
        private readonly InvoiceStorage $storage,
        private readonly AssignmentStorage $assignmentStorage,
    ) {
    }

    /**
     * @return list<mixed>
     */
    public function listInvoices(int $type, string $search): array
    {
        $invoices = [];
        foreach ($search ? $this->storage->fetchSome($type, $search) : $this->storage->fetchAll($type) as $invoice) {
            assert(is_array($invoice));
            $invoices[] = [
                'id' => (int) $invoice['id'],
                'type' => (int) $invoice['type'],
                'date' => $invoice['date'],
                'description' => $invoice['description'],
                'amount' => (float) $invoice['amount'],
                'assigned' => (bool) $invoice['closed'],
                'reference' => $invoice['reference'] ?? null,
                'document' => false,
                'no_document' => (bool) $invoice['no_document'],
                'finished' => (bool) $invoice['finished'],
            ];
        }
        return $invoices;
    }

    /**
     * @return list<mixed>
     */
    public function listOpenInvoices(): array
    {
        $invoices = [];
        foreach ($this->storage->fetchOpen() as $invoice) {
            assert(is_array($invoice));
            $invoices[] = [
                'id' => (int) $invoice['id'],
                'type' => (int) $invoice['type'],
                'date' => $invoice['date'],
                'description' => $invoice['description'],
                'amount' => (float) $invoice['amount'],
                'reference' => $invoice['reference'],
            ];
        }
        return $invoices;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInvoice(int $id): ?array
    {
        $invoice = $this->storage->fetchOne($id);
        if (!$invoice) {
            return null;
        }
        $contact = array_filter([
            'id' => $invoice['contact_id'] ?? null,
            'address' => $invoice['contact_address'] ?? null,
        ]);
        return [
            'id' => (int) $invoice['id'],
            'type' => (int) $invoice['type'],
            'date' => $invoice['date'],
            'description' => $invoice['description'],
            'amount' => (float) $invoice['amount'],
            'assigned' => (bool) $invoice['closed'],
            'ledgers' => [],
            'reference' => $invoice['reference'] ?? null,
            'document' => null,
            'no_document' => (bool) $invoice['no_document'],
            'contact' => $contact ?: null,
            'finished' => (bool) $invoice['finished'],
        ];
    }

    public function removeInvoice(int $id): bool
    {
        return (bool) $this->storage->removeOpenInvoice($id);
    }

    /**
     * @param array<string, int|string> $data
     */
    public function saveInvoice(array $data): bool
    {
        if ($data['id'] ?? null) {
            $invoice = $this->storage->fetchOne((int) $data['id']);
            if (!$invoice) {
                return false;
            }
            if ($invoice['closed'] || $invoice['finished']) {
                $this->storage->updateDetails(
                    (int) $data['id'],
                    (string) ($data['description'] ?? ''),
                    (string) ($data['reference'] ?? ''),
                    (bool) ($data['no_document'] ?? false),
                );
            } else {
                $this->storage->updateAll(
                    (int) $data['id'],
                    (string) ($data['date'] ?? ''),
                    (float) ($data['amount'] ?? 0.0),
                    (string) ($data['description'] ?? ''),
                    (string) ($data['reference'] ?? ''),
                    (bool) ($data['no_document'] ?? false),
                    (string) ($data['contact_address'] ?? ''),
                );
            }
        } else {
            if (!($data['type'] ?? null)) {
                return false;
            }
            $this->storage->create(
                (int) $data['type'],
                (string) ($data['date'] ?? ''),
                (float) ($data['amount'] ?? 0.0),
                (string) ($data['description'] ?? ''),
                (string) ($data['reference'] ?? ''),
                (bool) ($data['no_document'] ?? false),
                (string) ($data['contact_address'] ?? ''),
                isset($data['contact_id']) ? (int) $data['contact_id'] : null,
            );
        }
        return true;
    }

    /**
     * @param list<int> $ledgerIds
     */
    public function assignLedgers(int $invoiceId, array $ledgerIds): void
    {
        $this->assignmentStorage->transactional(function () use ($invoiceId, $ledgerIds) {
            $invoice = $this->assignmentStorage->fetchOpenInvoice($invoiceId);
            if (!$invoice) {
                throw new DomainException('invoice does not exist', 400);
            }
            $amount = 0.0;
            foreach ($ledgerIds as $ledgerId) {
                $ledger = $this->assignmentStorage->fetchOpenLedger($ledgerId);
                if (!$ledger) {
                    throw new DomainException('ledger does not exist', 400);
                }
                $this->assignmentStorage->createAssignment($ledgerId, $invoiceId);
                $this->assignmentStorage->markLedgerClosed($ledgerId);
                $amount += $ledger['amount'];
            }
            assert(is_numeric($invoice['amount']));
            if ((float) $invoice['amount'] !== $amount) {
                throw new DomainException('amounts do not match', 400);
            }
            $this->assignmentStorage->markInvoiceClosed($invoiceId);
            return true;
        });
    }
}
