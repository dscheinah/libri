<?php

namespace App\Handler\Report;

use App\Helper\Pdf;
use App\Repository\ReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;

/**
 * Handler for generating a report of potential booking problems (PDF).
 */
class ReportProblemHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {
    }

    /**
     * Handles the request to generate the problem report.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pdf = new Pdf(outputName: date('Y-m-d') . '_Probleme.pdf');
        $this->repository->renderProblemReport($pdf);
        $pdf->finish();
        return new Response();
    }
}
