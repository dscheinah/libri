<?php

namespace App\Repository;

use App\Helper\Pdf;
use App\Storage\AccountStorage;
use App\Storage\CategoryStorage;
use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use ZipStream\ZipStream;

/**
 * Use this repository to generate PDF and ZIP reports for accounts, categories, cancellations, etc.
 */
class ReportRepository
{
    private string $style = '
        <style>
            table {
                border: 1px solid #dddddd;
            }

            tfoot tr {
                background: white;
                font-weight: bold;
            }

            th, td {
                padding: 5px;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .amount {
                text-align: right;
            }
        </style>
    ';

    private string $summaryHeader = '
        <thead>
            <tr style="background-color: #dddddd;">
                <th>Konto</th>
                <th class="amount">Startwert</th>
                <th class="amount">Endwert</th>
                <th class="amount">Änderung</th>
                <th class="amount">Soll</th>
                <th class="amount">Haben</th>
            </tr>
        </thead>
    ';

    private string $ledgerHeader = '
        <colgroup>
            <col style="width: 75px;"/>
            <col style="width: 100px;"/>
            <col style="width: 75px;"/>
            <col style="width: 205px;"/>
            <col style="width: 113px;"/>
            <col style="width: 113px;"/>
        </colgroup>
        <thead>
            <tr style="background-color: #dddddd;">
                <th style="width: 75px;">Nummer</th>
                <th style="width: 100px;">Datum</th>
                <th style="width: 75px;">Konto</th>
                <th style="width: 205px;">Beschreibung/Referenz</th>
                <th style="width: 113px;" class="amount">Soll</th>
                <th style="width: 113px;" class="amount">Haben</th>
            </tr>
        </thead>
    ';

    private string $invoiceHeader = '
        <colgroup>
            <col style="width: 75px;"/>
            <col style="width: 100px;"/>
            <col style="width: 195px;"/>
            <col style="width: 195px;"/>
            <col style="width: 113px;"/>
        </colgroup>
        <thread>
            <tr style="background-color: #dddddd;">
                <td style="width: 75px;">Nummer</td>
                <td style="width: 100px;">Datum</td>
                <td style="width: 196px;">Dokument</td>
                <td style="width: 196px;">Beschreibung/Referenz</td>
                <th style="width: 113px;" class="amount">Betrag</th>
            </tr>
        </thread>
    ';

    private string $cancellationHeader = '
        <colgroup>
            <col style="width: 75px;"/>
            <col style="width: 100px;"/>
            <col style="width: 75px;"/>
            <col style="width: 205px;"/>
            <col style="width: 113px;"/>
            <col style="width: 113px;"/>
        </colgroup>
        <thread>
            <tr style="background-color: #dddddd;">
                <td style="width: 75px;">Nummer</td>
                <td style="width: 100px;">Datum</td>
                <th style="width: 75px;">Konto</th>
                <td style="width: 205px;">Stornierungsgrund</td>
                <th style="width: 113px;" class="amount">Soll</th>
                <th style="width: 113px;" class="amount">Haben</th>
            </tr>
        </thread>
    ';

    public function __construct(
        private readonly AccountStorage $accountStorage,
        private readonly CategoryStorage $categoryStorage,
        private readonly InvoiceStorage $invoiceStorage,
        private readonly LedgerStorage $ledgerStorage,
    ) {
    }

    /**
     * Renders a report for specific accounts within a date range into a PDF.
     * Use this method to generate account statements or journals.
     *
     * @param Pdf          $pdf      The PDF helper instance.
     * @param string       $start    Start date (YYYY-MM-DD).
     * @param string       $end      End date (YYYY-MM-DD).
     * @param list<string> $accounts List of account numbers to include.
     */
    public function renderAccountReport(Pdf $pdf, string $start, string $end, array $accounts): void
    {
        $header = $this->applyOutputTranslation(['start' => $start, 'end' => $end]);

        $summaryRows = '';
        $content = '';

        $totals = ['start_amount' => 0.0, 'end_amount' => 0.0, 'total' => 0.0, 'expense' => 0.0, 'income' => 0.0];

        foreach ($accounts as $no) {
            $account = $this->accountStorage->fetchOne($no);
            if (!$account) {
                continue;
            }

            $title = implode(' - ', array_filter([$account['no'], $account['name']]));

            $ledgerSummary = [
                'start_amount' => $this->ledgerStorage->fetchAmountBeforeDateByAccount($no, $start),
                'end_amount' => $this->ledgerStorage->fetchAmountAtDateByAccount($no, $end),
                ...$this->ledgerStorage->fetchSummaryByAccount($no, $start, $end),
            ];
            $summaryRows .= $this->renderSummaryRow($title, $ledgerSummary);
            foreach ($ledgerSummary as $key => $value) {
                $totals[$key] += $value;
            }

            $ledgerRows = '';
            foreach ($this->ledgerStorage->fetchForReportByAccount($no, $start, $end) as $ledger) {
                assert(is_array($ledger));
                $ledgerRows .= $this->renderLedgerRow($ledger);
            }
            $content .= $this->renderLedgerTable($title, $ledgerRows);
        }

        $pdf->addHTMLCell(
            html: "
                {$this->style}
                <h1>Bericht - Gesamtabrechnung</h1>
                <p><strong>{$header['_start']} bis {$header['_end']}</strong></p>
                <table>
                    {$this->summaryHeader}
                    $summaryRows
                    <tfoot>{$this->renderSummaryRow('', $totals)}</tfoot>
                </table>
                $content
            ",
            posx: 15,
            posy: 15,
            width: 180,
        );
    }

    /**
     * Renders an attachment report, providing a PDF summary and a ZIP archive of documents.
     * Use this method to export all invoice documents for a specific period.
     *
     * @param Pdf       $pdf   The PDF helper instance for the summary.
     * @param ZipStream $zip   The ZipStream instance to collect documents.
     * @param string    $start Start date (YYYY-MM-DD).
     * @param string    $end   End date (YYYY-MM-DD).
     */
    public function renderAttachmentReport(Pdf $pdf, ZipStream $zip, string $start, string $end): void
    {
        $header = $this->applyOutputTranslation(['start' => $start, 'end' => $end]);

        $invoiceRows = '';

        foreach ($this->invoiceStorage->fetchForReport($start, $end) as $invoice) {
            assert(is_array($invoice));
            if ($invoice['document']) {
                $name = "{$invoice['id']}_{$invoice['document_name']}";
                $zip->addFile($name, (string) $invoice['document']);
                $invoiceRows .= $this->renderInvoiceRow($invoice, $name);
            } else {
                $invoiceRows .= $this->renderInvoiceRow($invoice, '');
            }
        }

        $pdf->addHTMLCell(
            html: "
                {$this->style}
                <h1>Bericht - Dokumente</h1>
                <p><strong>{$header['start']} bis {$header['end']}</strong></p>
                {$this->renderInvoiceTable('Belege & Rechnungen', $invoiceRows)}
            ",
            posx: 15,
            posy: 15,
            width: 180,
        );
    }

    /**
     * Renders a report of all canceled ledger entries within a date range.
     * Use this method to audit canceled transactions.
     *
     * @param Pdf    $pdf   The PDF helper instance.
     * @param string $start Start date (YYYY-MM-DD).
     * @param string $end   End date (YYYY-MM-DD).
     */
    public function renderCancellationReport(Pdf $pdf, string $start, string $end): void
    {
        $header = $this->applyOutputTranslation(['start' => $start, 'end' => $end]);

        $cancellationRows = '';

        foreach ($this->ledgerStorage->fetchCanceled($start, $end) as $ledger) {
            assert(is_array($ledger));
            $cancellationRows .= $this->renderCancellationRow($ledger);
        }

        $pdf->addHTMLCell(
            html: "
                {$this->style}
                <h1>Bericht - Stornierte Buchungen</h1>
                <p><strong>{$header['start']} bis {$header['end']}</strong></p>
                <table>
                    {$this->cancellationHeader}
                    $cancellationRows
                </table>
            ",
            posx: 15,
            posy: 15,
            width: 180,
        );
    }

    /**
     * Renders a report for specific categories within a date range into a PDF.
     * Use this method to analyze spending or income by category.
     *
     * @param Pdf       $pdf        The PDF helper instance.
     * @param string    $start      Start date (YYYY-MM-DD).
     * @param string    $end        End date (YYYY-MM-DD).
     * @param list<int> $categories List of category IDs to include.
     */
    public function renderCategoryReport(Pdf $pdf, string $start, string $end, array $categories): void
    {
        $header = $this->applyOutputTranslation(['start' => $start, 'end' => $end]);

        $summaryRows = '';
        $content = '';

        $totals = ['start_amount' => 0.0, 'end_amount' => 0.0, 'total' => 0.0, 'expense' => 0.0, 'income' => 0.0];

        foreach ($categories as $id) {
            $id = (int) $id;
            $category = $this->categoryStorage->fetchOne($id);
            if (!$category) {
                continue;
            }

            $ledgerSummary = [
                'start_amount' => $this->ledgerStorage->fetchAmountBeforeDateByCategory($id, $start),
                'end_amount' => $this->ledgerStorage->fetchAmountAtDateByCategory($id, $end),
                ...$this->ledgerStorage->fetchSummaryByCategory($id, $start, $end),
            ];
            $summaryRows .= $this->renderSummaryRow((string) $category['name'], $ledgerSummary);
            foreach ($ledgerSummary as $key => $value) {
                $totals[$key] += $value;
            }

            $ledgerRows = '';
            foreach ($this->ledgerStorage->fetchForReportByCategory($id, $start, $end) as $ledger) {
                assert(is_array($ledger));
                $ledgerRows .= $this->renderLedgerRow($ledger);
            }
            $content .= $this->renderLedgerTable((string) $category['name'], $ledgerRows);
        }

        $pdf->addHTMLCell(
            html: "
                {$this->style}
                <h1>Bericht - Kategorien</h1>
                <p><strong>{$header['start']} bis {$header['end']}</strong></p>
                <table>
                    {$this->summaryHeader}
                    $summaryRows
                    <tfoot>{$this->renderSummaryRow('', $totals)}</tfoot>
                </table>
                $content
            ",
            posx: 15,
            posy: 15,
            width: 180,
        );
    }

    /**
     * Renders a problem report highlighting discrepancies (e.g., unassigned invoices or ledgers).
     * Use this method for data consistency checks.
     *
     * @param Pdf $pdf The PDF helper instance.
     */
    public function renderProblemReport(Pdf $pdf): void
    {
        $unassignedLedgerRows = '';
        $unassignedInvoiceRows = '';
        $noDocumentInvoiceRows = '';

        foreach ($this->ledgerStorage->fetchUnassigned() as $ledger) {
            assert(is_array($ledger));
            $unassignedLedgerRows .= $this->renderLedgerRow($ledger);
        }

        foreach ($this->invoiceStorage->fetchUnassigned() as $invoice) {
            assert(is_array($invoice));
            $unassignedInvoiceRows .= $this->renderInvoiceRow($invoice, (string) $invoice['document_name']);
        }

        foreach ($this->invoiceStorage->fetchWithoutDocument() as $invoice) {
            assert(is_array($invoice));
            $noDocumentInvoiceRows .= $this->renderInvoiceRow($invoice, (string) $invoice['document_name']);
        }

        $pdf->addHTMLCell(
            html: "
                {$this->style}
                <h1>Bericht - Probleme</h1>
                {$this->renderLedgerTable('Buchungen ohne Beleg', $unassignedLedgerRows)}
                {$this->renderInvoiceTable('Offene Belege & Rechnungen', $unassignedInvoiceRows)}
                {$this->renderInvoiceTable('Fehlende Dokumente', $noDocumentInvoiceRows)}
            ",
            posx: 15,
            posy: 15,
            width: 180,
        );
    }

    /**
     * @param array<string, string|float|null> $input
     *
     * @return array<string, string|float|null>
     */
    private function applyOutputTranslation(array $input): array
    {
        foreach ($input as $key => $value) {
            $input["_$key"] = match ($key) {
                'date', 'start', 'end'
                    => date('d.m.Y', strtotime((string) $value) ?: null),
                'amount', 'expense', 'income', 'total', 'start_amount', 'end_amount'
                    => number_format((float) $value, 2, ',', '.'),
                default
                    => $value,
            };
        }
        return $input;
    }

    /**
     * @param array<string, string|float|null> $summary
     */
    private function renderSummaryRow(string $title, array $summary): string
    {
        $summary = $this->applyOutputTranslation($summary);
        return "
            <tr>
                <td>$title</td>
                <td class='amount'>{$summary['_start_amount']}</td>
                <td class='amount'>{$summary['_end_amount']}</td>
                <td class='amount'>{$summary['_total']}</td>
                <td class='amount'>{$summary['_expense']}</td>
                <td class='amount'>{$summary['_income']}</td>
            </tr>
        ";
    }

    /**
     * @param array<string, string|float|null> $ledger
     */
    private function renderLedgerRow(array $ledger): string
    {
        $ledger = $this->applyOutputTranslation($ledger);
        $account = $ledger['offset_no'] ?? $ledger['account_no'] ?? '';
        $expense = $ledger['amount'] < 0 ? $ledger['_amount'] : '';
        $income = $ledger['amount'] > 0 ? $ledger['_amount'] : '';
        return "
            <tr>
                <td>{$ledger['id']}</td>
                <td>{$ledger['_date']}</td>
                <td>$account</td>
                <td>
                    {$ledger['description']}<br>
                    {$ledger['reference']}
                </td>
                <td class='amount'>$expense</td>
                <td class='amount'>$income</td>
            </tr>
        ";
    }

    private function renderLedgerTable(string $title, string $body): string
    {
        return "
            <h2>$title</h2>
            <table>
                {$this->ledgerHeader}
                $body
            </table>
        ";
    }

    private function renderInvoiceTable(string $title, string $body): string
    {
        return "
            <h2>$title</h2>
            <table>
                {$this->invoiceHeader}
                $body
            </table>
        ";
    }

    /**
     * @param array<string, string|float|int|null> $invoice
     */
    private function renderInvoiceRow(array $invoice, ?string $name): string
    {
        $invoice = $this->applyOutputTranslation($invoice);
        if (!$name && $invoice['no_document']) {
            $name = '<i>Eigenbeleg</i>';
        }
        return "
            <tr>
                <td>{$invoice['id']}</td>
                <td>{$invoice['_date']}</td>
                <td>$name</td>
                <td>
                    {$invoice['description']}<br>
                    {$invoice['reference']}
                </td>
                <td class='amount'>{$invoice['_amount']}</td>
            </tr>
        ";
    }

    /**
     * @param array<string, string|float|null> $ledger
     */
    private function renderCancellationRow(array $ledger): string
    {
        $ledger = $this->applyOutputTranslation($ledger);
        $expense = $ledger['amount'] < 0 ? $ledger['_amount'] : '';
        $income = $ledger['amount'] > 0 ? $ledger['_amount'] : '';
        return "
            <tr>
                <td>{$ledger['id']}</td>
                <td>{$ledger['_date']}</td>
                <td>{$ledger['account_no']}</td>
                <td>{$ledger['canceled_reason']}</td>
                <td class='amount'>$expense</td>
                <td class='amount'>$income</td>
            </tr>
        ";
    }
}
