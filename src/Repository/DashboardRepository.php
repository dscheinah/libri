<?php

namespace App\Repository;

use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;

/**
 * Repository for dashboard-related data aggregation.
 */
class DashboardRepository
{
    public function __construct(
        private readonly InvoiceStorage $invoiceStorage,
        private readonly LedgerStorage $ledgerStorage,
    ) {
    }

    /**
     * Calculates the total balance across all real accounts.
     *
     * @return list<array<string, string|float>> A list of real accounts with their names and balances.
     */
    public function accounts(): array
    {
        $accounts = [];
        foreach ($this->ledgerStorage->sumRealAccounts() as $account) {
            assert(is_array($account));
            $accounts[] = [
                'name' => implode(' - ', array_filter([$account['no'], $account['name']])),
                'amount' => (float) $account['sum'],
            ];
        }
        return $accounts;
    }

    /**
     * Aggregates ledger sums by category for the dashboard.
     *
     * @return list<array<string, string|float>> A list of categories with their names and total amounts.
     */
    public function categories(): array
    {
        $categories = [];
        foreach ($this->ledgerStorage->sumCategories() as $category) {
            assert(is_array($category));
            $categories[] = [
                'name' => (string) ($category['name'] ?: 'ohne Zuordnung'),
                'amount' => (float) $category['sum'],
            ];
        }
        return $categories;
    }

    /**
     * Identifies potential problems such as unassigned bookings or missing documents.
     *
     * @return list<array<string, string|int>> A list of problem areas and their counts.
     */
    public function problems(): array
    {
        return [
            [
                'name' => 'Buchungen ohne Beleg',
                'count' => $this->ledgerStorage->countUnassigned(),
            ],
            [
                'name' => 'Offene Belege & Rechnungen',
                'count' => $this->invoiceStorage->countUnassigned(),
            ],
            [
                'name' => 'Fehlende Dokumente',
                'count' => $this->invoiceStorage->countWithoutDocument(),
            ],
        ];
    }
}
