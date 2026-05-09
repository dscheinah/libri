<?php

namespace App\Handler\Report;

use App\Repository\ReportRepository;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ReportHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): mixed
    {
        return new $class(
            $injector->get(ReportRepository::class),
        );
    }
}
