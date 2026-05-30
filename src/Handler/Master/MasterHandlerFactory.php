<?php

namespace App\Handler\Master;

use App\Repository\MasterRepository;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * Factory for master data-related handlers.
 */
class MasterHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): mixed
    {
        return new $class(
            $injector->get(ResponseHelperInterface::class),
            $injector->get(MasterRepository::class),
        );
    }
}
