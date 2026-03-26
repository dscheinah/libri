<?php

namespace App\Repository;

use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;

class DashboardRepository
{
    public function __construct(
        private readonly InvoiceStorage $invoiceStorage,
        private readonly LedgerStorage $ledgerStorage,
    ) {
    }

    public function accounts(): float
    {
        return array_sum(array_column(iterator_to_array($this->ledgerStorage->sumRealAccounts()), 'sum'));
    }

    /**
     * @return list<array<string, string|float>>
     */
    public function categories(): array
    {
        $categories = [];
        foreach ($this->ledgerStorage->sumCategories() as $category) {
            assert(is_array($category));
            $categories[] = [
                'name' => $category['name'] ?: 'ohne Zuordnung',
                'amount' => $category['sum'],
            ];
        }
        return $categories;
    }

    /**
     * @return list<array<string, string|int>>
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
