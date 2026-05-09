<?php

namespace App\Repository;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ReportRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): ReportRepository
    {
        return new ReportRepository();
    }
}
