<?php

namespace App\Handler\Report;

use App\Helper\Pdf;
use App\Repository\ReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;
use ZipStream\ZipStream;

class ReportAttachmentHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (!isset($queryParams['start'], $queryParams['end'])) {
            return (new Response())->withStatus(400);
        }

        $date = date('Y-m-d');
        $zip = new ZipStream(outputName: $date . '_Dokumente.zip');
        $pdf = new Pdf(outputName: $date . '_Dokumente.pdf');

        $this->repository->renderAttachmentReport(
            $pdf,
            $zip,
            (string) $queryParams['start'],
            (string) $queryParams['end'],
        );

        $zip->addFile($date . '_Dokumente.pdf', $pdf->getOutPDFString());
        $zip->finish();

        return new Response();
    }
}
