<?php

namespace App\Handler\Ledger;

use App\Repository\LedgerRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerSaveHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseHelperInterface $helper,
        private readonly LedgerRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        $dataMissing = !isset(
            $data['date'],
            $data['account'],
            $data['offset'],
            $data['amount'],
            $data['description'],
            $data['reference'],
        );
        if ($dataMissing) {
            return $this->helper->create(400);
        }

        $dates = array_values((array) $data['date']);
        $accounts = array_values((array) $data['account']);
        $offsets = array_values((array) $data['offset']);
        $amounts = array_values((array) $data['amount']);
        $descriptions = array_values((array) $data['description']);
        $references = array_values((array) $data['reference']);

        $counts = array_unique(array_map(count(...), [$dates, $accounts, $amounts, $descriptions, $references]));
        if (count($counts) > 1) {
            return $this->helper->create(400);
        }

        $this->repository->createLedgers(
            current($counts),
            $dates,
            $accounts,
            $offsets,
            $amounts,
            $descriptions,
            $references,
        );
        return $this->helper->create(204);
    }
}
