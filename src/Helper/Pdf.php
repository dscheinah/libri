<?php

namespace App\Helper;

use Com\Tecnick\Pdf\Tcpdf;

class Pdf extends Tcpdf
{
    public function __construct(string $outputName)
    {
        parent::__construct();
        $this->setPDFFilename($outputName);
        $this->font->insert($this->pon, 'helvetica', '', 10);
        $this->addPage();
    }

    public function finish(): void
    {
        $this->downloadPDF($this->getOutPDFString());
    }
}
