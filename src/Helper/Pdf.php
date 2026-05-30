<?php

namespace App\Helper;

use Com\Tecnick\Pdf\Tcpdf;

/**
 * A helper class extending Tcpdf for generating and downloading PDF documents.
 */
class Pdf extends Tcpdf
{
    /**
     * Creates a new PDF document with a default font and a first page.
     *
     * @param string $outputName The filename for the PDF download.
     */
    public function __construct(string $outputName)
    {
        parent::__construct();
        $this->setPDFFilename($outputName);
        $this->font->insert($this->pon, 'helvetica', '', 10);
        $this->addPage();
    }

    /**
     * Finalizes the PDF document and triggers a browser download.
     */
    public function finish(): void
    {
        $this->downloadPDF($this->getOutPDFString());
    }
}
