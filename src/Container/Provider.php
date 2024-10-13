<?php

namespace App\Container;

use App\ApplicationFactory;
use App\Handler\Account\AccountHandlerFactory;
use App\Handler\Account\AccountListHandler;
use App\Handler\Account\AccountListRealHandler;
use App\Handler\Account\AccountSaveHandler;
use App\Handler\Category\CategoryHandlerFactory;
use App\Handler\Category\CategoryListHandler;
use App\Handler\Category\CategorySaveHandler;
use App\Handler\Contact\ContactHandlerFactory;
use App\Handler\Contact\ContactListHandler;
use App\Handler\Contact\ContactLoadHandler;
use App\Handler\Contact\ContactRemoveHandler;
use App\Handler\Contact\ContactSaveHandler;
use App\Handler\Invoice\InvoiceHandlerFactory;
use App\Handler\Invoice\InvoiceListAssignableHandler;
use App\Handler\Invoice\InvoiceListHandler;
use App\Handler\Invoice\InvoiceLoadHandler;
use App\Handler\Invoice\InvoiceRemoveHandler;
use App\Handler\Invoice\InvoiceSaveAssignHandler;
use App\Handler\Invoice\InvoiceSaveHandler;
use App\Handler\Ledger\LedgerHandlerFactory;
use App\Handler\Ledger\LedgerListAssignableHandler;
use App\Handler\Ledger\LedgerListHandler;
use App\Handler\Ledger\LedgerLoadHandler;
use App\Handler\Ledger\LedgerRemoveHandler;
use App\Handler\Ledger\LedgerSaveAssignHandler;
use App\Handler\Ledger\LedgerSaveHandler;
use App\Handler\ListHandler;
use App\Handler\ListHandlerFactory;
use App\Handler\Master\MasterHandlerFactory;
use App\Handler\Master\MasterLoadHandler;
use App\Handler\Master\MasterSaveHandler;
use App\Handler\Report\DashboardHandler;
use App\Handler\Report\DashboardHandlerFactory;
use App\RouterFactory;
use Sx\Application\Container\ApplicationProvider;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Log\Container\LogProvider;
use Sx\Message\Container\MessageProvider;
use Sx\Server\ApplicationInterface;
use Sx\Server\Container\ServerProvider;
use Sx\Server\RouterInterface;

/**
 * This class is used in index.php to setup the dependency injector.
 */
class Provider implements ProviderInterface
{
    /**
     * Adds all used mappings for interfaces and classes to factories.
     *
     * @param Injector $injector
     */
    public function provide(Injector $injector): void
    {
        // First do a setup of all modules installed by composer.
        $injector->setup(new ApplicationProvider());
        $injector->setup(new LogProvider());
        $injector->setup(new MessageProvider());
        $injector->setup(new ServerProvider());
        // Add all local classes and factories.
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(ListHandler::class, ListHandlerFactory::class);

        $injector->set(DashboardHandler::class, DashboardHandlerFactory::class);

        $injector->set(AccountListHandler::class, AccountHandlerFactory::class);
        $injector->set(AccountListRealHandler::class, AccountHandlerFactory::class);
        $injector->set(AccountSaveHandler::class, AccountHandlerFactory::class);

        $injector->set(CategoryListHandler::class, CategoryHandlerFactory::class);
        $injector->set(CategorySaveHandler::class, CategoryHandlerFactory::class);

        $injector->set(ContactListHandler::class, ContactHandlerFactory::class);
        $injector->set(ContactLoadHandler::class, ContactHandlerFactory::class);
        $injector->set(ContactRemoveHandler::class, ContactHandlerFactory::class);
        $injector->set(ContactSaveHandler::class, ContactHandlerFactory::class);

        $injector->set(InvoiceListAssignableHandler::class, InvoiceHandlerFactory::class);
        $injector->set(InvoiceListHandler::class, InvoiceHandlerFactory::class);
        $injector->set(InvoiceLoadHandler::class, InvoiceHandlerFactory::class);
        $injector->set(InvoiceRemoveHandler::class, InvoiceHandlerFactory::class);
        $injector->set(InvoiceSaveAssignHandler::class, InvoiceHandlerFactory::class);
        $injector->set(InvoiceSaveHandler::class, InvoiceHandlerFactory::class);

        $injector->set(LedgerListAssignableHandler::class, LedgerHandlerFactory::class);
        $injector->set(LedgerListHandler::class, LedgerHandlerFactory::class);
        $injector->set(LedgerLoadHandler::class, LedgerHandlerFactory::class);
        $injector->set(LedgerRemoveHandler::class, LedgerHandlerFactory::class);
        $injector->set(LedgerSaveAssignHandler::class, LedgerHandlerFactory::class);
        $injector->set(LedgerSaveHandler::class, LedgerHandlerFactory::class);

        $injector->set(MasterLoadHandler::class, MasterHandlerFactory::class);
        $injector->set(MasterSaveHandler::class, MasterHandlerFactory::class);
    }
}
