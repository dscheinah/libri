<?php

namespace App;

use Sx\Application\Middleware\ErrorHandler;
use Sx\Application\Middleware\NotFoundHandler;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\Application;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;

/**
 * The factory for the application. It defines the middleware chain.
 */
class ApplicationFactory implements FactoryInterface
{
    /**
     * Creates the application and adds all middleware in correct order.
     *
     * @param Injector             $injector
     * @param array<string, mixed> $options
     * @param string               $class
     *
     * @return Application
     */
    public function create(Injector $injector, array $options, string $class): Application
    {
        $handler = $injector->get(MiddlewareHandlerInterface::class);
        assert($handler instanceof MiddlewareHandlerInterface);
        $app = new Application($handler);
        // Add the error handler first as it wraps all calls in a big try/ catch.
        // Only exceptions are handled so you should avoid triggering other errors or warnings.
        $app->add(ErrorHandler::class);
        // The router will split the request to more handlers defined it the App\RouterFactory.
        $app->add(RouterInterface::class);
        // The last handler returns a simple 404 response. It is only called if no route matches.
        $app->add(NotFoundHandler::class);
        return $app;
    }
}
