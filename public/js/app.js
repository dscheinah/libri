import Action from '../vendor/dscheinah/sx-js/src/Action.js';
import Page from '../vendor/dscheinah/sx-js/src/Page.js';
import State from '../vendor/dscheinah/sx-js/src/State.js';
import init from './app/init.js';
import navigate from './app/navigate.js';
import * as account from './repository/account.js';
import * as category from './repository/category.js';
import * as contact from './repository/contact.js';
import * as ledger from './repository/ledger.js';
import * as invoice from './repository/invoice.js';
import * as master from './repository/master.js';
import * as report from './repository/report.js';
import * as data from './repository/data.js';
// By separating the helpers to it's own namespace they do not need to packed to an object here.
import * as helper from './helper.js';

// Create the global event listener (on window) to be used for e.g. navigation.
const action = new Action(window);
// The repository that will handle the requests to the backend.
// Create the global state manager.
const state = new State();
// Create the page manager responsible for lazy loading pages and handling the history and page stack.
// The state manager is used to trigger sx-show and sx-hide when the state of pages changes.
// The state event gets the ID of the page as payload.
const page = new Page(state, helper.element('#main'));

// Populate the initial application state.
init(state);

// Handle the global navigation. This also handles links in pages automatically.
// To add a link use <button value="${id}" data-navigation>...</button>.
// The IDs must correspond with the pages defined later in this file.
action.listen('[data-navigation]', 'click', (event, target) => navigate(state, page, event, target));
// The navigation-back button is invisible but keyboard controllable.
action.listen('#navigation-back', 'click', () => history.back());

// A global state handler to show the loading animation.
// Use state.dispatch('loading', true) to trigger the animation and state.dispatch('loading', false) to stop it.
state.handle('loading', (payload, next) => {
    // The element is hidden by using visibility to not need extra CSS for positioning of the menu entries.
    helper.style('#loading', 'visibility', payload ? null : 'hidden');
    return next(payload);
});
// Always disable the loading animation when any loaded page is ready.
state.listen('sx-show', () => state.dispatch('loading', false));

// Add a common loading state handler to all async repository actions.
[
    'accounts',
    'categories',
    'contacts',
    'contact',
    'dashboard',
    'ledgers',
    'ledgers-assignable',
    'ledger',
    'invoices',
    'invoices-assignable',
    'invoice',
    'master',
].forEach((key) => {
    state.handle(key, async (payload, next) => {
        try {
            state.dispatch('loading', true);
            return await next(payload);
        } finally {
            state.dispatch('loading', false);
        }
    });
});
// Handle async repository calls.
state.handle('accounts', account.list);
state.handle('categories', category.list);
state.handle('contacts', contact.list);
state.handle('contact', contact.get);
state.handle('dashboard', report.dashboard);
state.handle('ledgers', ({account, search}) => ledger.list(account, search));
state.handle('ledgers-assignable', ledger.assignable);
state.handle('ledger', ledger.get);
state.handle('invoices', ({type, search}) => invoice.list(type, search));
state.handle('invoices-assignable', invoice.assignable);
state.handle('invoice', invoice.get);
state.handle('master', master.get);

// This is a simple example for async global state management.
state.handle('backend-data', (payload) => data.load(payload));

// Define all pages and load the main page. The ID defined here is globally used for:
//  - handling navigation by href or value (see above)
//  - registering scopes in pages
//  - payload of sx-show and sx-hide state events
[
    'contacts',
    'contacts-details',
    'contacts-edit',
    'dashboard',
    'invoices',
    'invoices-assign',
    'invoices-details',
    'invoices-edit',
    'ledgers',
    'ledgers-assign',
    'ledgers-details',
    'ledgers-enter',
    'reports-accounts',
    'reports-attachments',
    'reports-cancellations',
    'reports-categories',
    'reports-problems',
    'settings',
    'settings-accounts',
    'settings-categories',
    'settings-master',
].forEach((key) => {
    page.add(key, `pages/${key.replace('-', '/')}.html`, window.location.href);
});
page.show('dashboard');

// The app.js file is used as a kind of service manager for dependency injection.
// Import the file in pages to get access to the exported modules.
export {helper, page, state};
