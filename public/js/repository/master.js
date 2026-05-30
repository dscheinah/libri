import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches master data settings from the backend.
 *
 * @returns {Promise<Object>}
 */
export async function get() {
    return fetchBackend('/master/load');
}

/**
 * Saves master data settings.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/master/save', {method: 'POST', body: data});
}
