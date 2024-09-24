<?php

namespace App;

use App\Handler\ListHandler;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\Router;

/**
 * The factory for the router. It defines all available routes.
 */
class RouterFactory implements FactoryInterface
{
    /**
     * Creates the router and registers all handlers for routes.
     *
     * @param Injector             $injector
     * @param array<string, mixed> $options
     * @param string               $class
     *
     * @return Router
     */
    public function create(Injector $injector, array $options, string $class): Router
    {
        // The prefix can be set in the config if the index.php is not available from "/".
        $prefix = $options['prefix'] ?? '';
        $handler = $injector->get(MiddlewareHandlerInterface::class);
        assert($handler instanceof MiddlewareHandlerInterface);
        $router = new Router($handler);
        // Add the example handler for the backend page.
        $router->post($prefix . 'list', ListHandler::class);
        return $router;
    }
}
