<?php

namespace App\Handler\Report;

use App\Repository\DashboardRepository;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

class DashboardHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string,mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): DashboardHandler
    {
        $helper = $injector->get(ResponseHelperInterface::class);
        assert($helper instanceof ResponseHelperInterface);
        $repository = $injector->get(DashboardRepository::class);
        assert($repository instanceof DashboardRepository);
        return new DashboardHandler(
            $helper,
            $repository,
        );
    }
}
