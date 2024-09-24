<?php

namespace App\Handler;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\ResponseHelperInterface;

/**
 * The factory for the list handler.
 */
class ListHandlerFactory implements FactoryInterface
{
    /**
     * Creates the handler with the default response helper.
     *
     * @param Injector             $injector
     * @param array<string, mixed> $options
     * @param string               $class
     *
     * @return ListHandler
     */
    public function create(Injector $injector, array $options, string $class): ListHandler
    {
        $helper = $injector->get(ResponseHelperInterface::class);
        assert($helper instanceof ResponseHelperInterface);
        return new ListHandler($helper);
    }
}
