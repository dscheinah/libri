import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches invoices by type and optional search term.
 *
 * @param {number|string} type - Invoice type (e.g., 1 for income, 2 for expense).
 * @param {string} search - Optional search term.
 *
 * @returns {Promise<Array>}
 */
export async function list(type, search) {
    const params = new URLSearchParams();
    params.set('type', type || '');
    params.set('search', search || '');
    return fetchBackend('/invoice/list?' + params.toString());
}

/**
 * Fetches a single invoice by ID.
 *
 * @param {number|string} id
 *
 * @returns {Promise<Object>}
 */
export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/invoice/load?' + params.toString());
}

/**
 * Fetches invoices that are assignable to ledger entries.
 *
 * @returns {Promise<Array>}
 */
export async function assignable() {
    return fetchBackend('/invoice/list-assignable');
}

/**
 * Saves or updates an invoice.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/invoice/save', {method: 'POST', body: data});
}

/**
 * Finalizes an invoice (e.g., generates PDF and lock updates).
 *
 * @param {number} id
 */
export async function finish(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/invoice/finish?' + params.toString(), {method: 'PUT'});
}

/**
 * Removes an invoice.
 *
 * @param {number} id
 */
export async function remove(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/invoice/remove?' + params.toString(), {method: 'DELETE'});
}

/**
 * Assigns ledger entries to an invoice.
 *
 * @param {FormData} data - Data containing invoice ID and ledger IDs.
 */
export async function assign(data) {
    return fetchBackend('/invoice/save-assign', {method: 'POST', body: data});
}
