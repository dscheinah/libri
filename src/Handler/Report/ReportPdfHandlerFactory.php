<?php

namespace App\Handler\Report;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ReportPdfHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): ReportPdfHandler
    {
        return new ReportPdfHandler();
    }
}
