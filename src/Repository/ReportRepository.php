<?php

namespace App\Repository;

use App\Helper\Pdf;
use ZipStream\ZipStream;

class ReportRepository
{
    public function __construct(
    ) {
    }

    /**
     * @param list<string> $accounts
     */
    public function renderAccountReport(Pdf $pdf, string $start, string $end, array $accounts): void
    {
        $startTranslated = date('d.m.Y', strtotime($start) ?: null);
        $endTranslated = date('d.m.Y', strtotime($end) ?: null);
        $pdf->addHTMLCell(
            "
                <h1>Bericht - Gesamtabrechnung</h1>
                <p>$startTranslated bis $endTranslated</p>
            ",
            15,
            15,
            180
        );
    }

    public function renderAttachmentReport(Pdf $pdf, ZipStream $zip, string $start, string $end): void
    {
        $startTranslated = date('d.m.Y', strtotime($start) ?: null);
        $endTranslated = date('d.m.Y', strtotime($end) ?: null);
        $pdf->addHTMLCell(
            "
                <h1>Bericht - Dokumente</h1>
                <p>$startTranslated bis $endTranslated</p>
            ",
            15,
            15,
            180
        );
    }

    public function renderCancellationReport(Pdf $pdf, string $start, string $end): void
    {
        $startTranslated = date('d.m.Y', strtotime($start) ?: null);
        $endTranslated = date('d.m.Y', strtotime($end) ?: null);
        $pdf->addHTMLCell(
            "
                <h1>Bericht - Stornierte Buchungen</h1>
                <p>$startTranslated bis $endTranslated</p>
            ",
            15,
            15,
            180
        );
    }

    /**
     * @param list<int|string> $categories
     */
    public function renderCategoryReport(Pdf $pdf, string $start, string $end, array $categories): void
    {
        $startTranslated = date('d.m.Y', strtotime($start) ?: null);
        $endTranslated = date('d.m.Y', strtotime($end) ?: null);
        $pdf->addHTMLCell(
            "
                <h1>Bericht - Kategorien</h1>
                <p>$startTranslated bis $endTranslated</p>
            ",
            15,
            15,
            180
        );
    }

    public function renderProblemReport(Pdf $pdf): void
    {
        $pdf->addHTMLCell(
            "
                <h1>Bericht - Probleme</h1>
            ",
            15,
            15,
            180
        );
    }
}
