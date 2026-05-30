import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches all categories from the backend.
 *
 * @returns {Promise<Array>}
 */
export async function list() {
    return fetchBackend('/category/list');
}

/**
 * Saves category definitions.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/category/save', {method: 'POST', body: data});
}
