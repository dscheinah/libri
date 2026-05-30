import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches ledger entries for a specific account.
 *
 * @param {string} account - Account number.
 * @param {string} search - Optional search term.
 *
 * @returns {Promise<Array>}
 */
export async function list(account, search) {
    const params = new URLSearchParams();
    params.set('account', account || '');
    params.set('search', search || '');
    return fetchBackend('/ledger/list?' + params.toString());
}

/**
 * Fetches a single ledger entry by ID.
 *
 * @param {number} id
 *
 * @returns {Promise<Object>}
 */
export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/ledger/load?' + params.toString());
}

/**
 * Fetches a list of ledger entries that can be assigned to invoices.
 *
 * @returns {Promise<Array>}
 */
export async function assignable() {
    return fetchBackend('/ledger/list-assignable');
}

/**
 * Saves one or more ledger entries.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/ledger/save', {method: 'POST', body: data});
}

/**
 * Cancels a ledger entry.
 *
 * @param {FormData} data - Data containing the ID and reason for cancellation.
 */
export async function cancel(data) {
    return fetchBackend('/ledger/remove', {method: 'POST', body: data});
}

/**
 * Assigns invoices to a ledger entry.
 *
 * @param {FormData} data - Data containing ledger ID and invoice IDs.
 */
export async function assign(data) {
    return fetchBackend('/ledger/save-assign', {method: 'POST', body: data});
}
