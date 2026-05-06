<?php

namespace App\Handler\Report;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ReportZipHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): ReportZipHandler
    {
        return new ReportZipHandler();
    }
}
