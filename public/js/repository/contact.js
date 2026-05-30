import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches a list of contacts from the backend.
 *
 * @param {string} search - Optional search term to filter contacts.
 *
 * @returns {Promise<Array>}
 */
export async function list(search) {
    const params = new URLSearchParams();
    params.set('search', search || '');
    return fetchBackend('/contact/list?' + params.toString());
}

/**
 * Fetches a single contact's details by ID.
 *
 * @param {number} id
 *
 * @returns {Promise<Object>}
 */
export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/contact/load?' + params.toString());
}

/**
 * Saves or updates a contact.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/contact/save', {method: 'POST', body: data});
}

/**
 * Removes a contact by ID.
 *
 * @param {number} id
 */
export async function remove(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/contact/remove?' + params.toString(), {method: 'DELETE'});
}
