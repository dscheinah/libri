<?php

namespace App\Handler\Ledger;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

class LedgerHandlerFactory implements FactoryInterface
{
    /**
     * @param array<string,mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): mixed
    {
        return new $class(
            $injector->get(ResponseHelperInterface::class),
        );
    }
}
