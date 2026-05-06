<?php

namespace App\Handler\Report;

use Com\Tecnick\Pdf\Tcpdf;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;

class ReportPdfHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pdf = new Tcpdf();
        $pdf->setPDFFilename('dummy.pdf');
        $pdf->font->insert($pdf->pon, 'helvetica', '', 12);
        $pdf->addPage();
        $pdf->downloadPDF($pdf->getOutPDFString());
        return new Response();
    }
}
