<?php

namespace App\Handler\Report;

use App\Helper\Pdf;
use App\Repository\ReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response;

/**
 * Handler to generate a PDF report for one or more categories.
 */
class ReportCategoryHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ReportRepository $repository,
    ) {
    }

    /**
     * Handles the request to generate a category report.
     * Expects 'start', 'end', and 'categories' (array) as query parameters.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (!isset($queryParams['start'], $queryParams['end'], $queryParams['categories'])) {
            return (new Response())->withStatus(400);
        }

        $pdf = new Pdf(outputName: date('Y-m-d') . '_Kategorien.pdf');
        $this->repository->renderCategoryReport(
            $pdf,
            (string) $queryParams['start'],
            (string) $queryParams['end'],
            (array) $queryParams['categories'],
        );
        $pdf->finish();
        return new Response();
    }
}
