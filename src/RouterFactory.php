<?php

namespace App;

use App\Handler\Account\AccountListHandler;
use App\Handler\Account\AccountListRealHandler;
use App\Handler\Account\AccountSaveHandler;
use App\Handler\Category\CategoryListHandler;
use App\Handler\Category\CategorySaveHandler;
use App\Handler\Contact\ContactListHandler;
use App\Handler\Contact\ContactLoadHandler;
use App\Handler\Contact\ContactRemoveHandler;
use App\Handler\Contact\ContactSaveHandler;
use App\Handler\Invoice\InvoiceListAssignableHandler;
use App\Handler\Invoice\InvoiceListHandler;
use App\Handler\Invoice\InvoiceLoadHandler;
use App\Handler\Invoice\InvoiceRemoveHandler;
use App\Handler\Invoice\InvoiceSaveAssignHandler;
use App\Handler\Invoice\InvoiceSaveHandler;
use App\Handler\Ledger\LedgerListAssignableHandler;
use App\Handler\Ledger\LedgerListHandler;
use App\Handler\Ledger\LedgerLoadHandler;
use App\Handler\Ledger\LedgerRemoveHandler;
use App\Handler\Ledger\LedgerSaveAssignHandler;
use App\Handler\Ledger\LedgerSaveHandler;
use App\Handler\ListHandler;
use App\Handler\Master\MasterLoadHandler;
use App\Handler\Master\MasterSaveHandler;
use App\Handler\Report\DashboardHandler;
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

        $router->get($prefix . 'dashboard', DashboardHandler::class);

        $router->get($prefix . 'account/list', AccountListHandler::class);
        $router->get($prefix . 'account/list-real', AccountListRealHandler::class);
        $router->post($prefix . 'account/save', AccountSaveHandler::class);

        $router->get($prefix . 'category/list', CategoryListHandler::class);
        $router->post($prefix . 'category/save', CategorySaveHandler::class);

        $router->get($prefix . 'contact/list', ContactListHandler::class);
        $router->get($prefix . 'contact/load', ContactLoadHandler::class);
        $router->post($prefix . 'contact/save', ContactSaveHandler::class);
        $router->delete($prefix . 'contact/remove', ContactRemoveHandler::class);

        $router->get($prefix . 'invoice/list', InvoiceListHandler::class);
        $router->get($prefix . 'invoice/list-assignable', InvoiceListAssignableHandler::class);
        $router->get($prefix . 'invoice/load', InvoiceLoadHandler::class);
        $router->post($prefix . 'invoice/save', InvoiceSaveHandler::class);
        $router->post($prefix . 'invoice/save-assign', InvoiceSaveAssignHandler::class);
        $router->delete($prefix . 'invoice/remove', InvoiceRemoveHandler::class);

        $router->get($prefix . 'ledger/list', LedgerListHandler::class);
        $router->get($prefix . 'ledger/list-assignable', LedgerListAssignableHandler::class);
        $router->get($prefix . 'ledger/load', LedgerLoadHandler::class);
        $router->post($prefix . 'ledger/save', LedgerSaveHandler::class);
        $router->post($prefix . 'ledger/save-assign', LedgerSaveAssignHandler::class);
        $router->post($prefix . 'ledger/remove', LedgerRemoveHandler::class);

        $router->get($prefix . 'master/load', MasterLoadHandler::class);
        $router->post($prefix . 'master/save', MasterSaveHandler::class);

        return $router;
    }
}
