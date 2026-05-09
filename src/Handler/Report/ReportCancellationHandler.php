<?php

namespace App\Handler\Report;

use App\Helper\Pdf;
use App\Repository\ReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;

class ReportCancellationHandler implements RequestHandlerInterface
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

        $pdf = new Pdf(outputName: date('Y-m-d') . '_Stornierte_Buchungen.pdf');
        $this->repository->renderCancellationReport(
            $pdf,
            (string) $queryParams['start'],
            (string) $queryParams['end'],
        );
        $pdf->finish();
        return new Response();
    }
}
