import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches all accounts from the backend.
 *
 * @returns {Promise<Array>}
 */
export async function list() {
    return fetchBackend('/account/list');
}

/**
 * Fetches only "real" (bank/cash) accounts from the backend.
 *
 * @returns {Promise<Array>}
 */
export async function real() {
    return fetchBackend('/account/list-real');
}

/**
 * Saves or updates account definitions.
 *
 * @param {FormData} data
 */
export async function save(data) {
    return fetchBackend('/account/save', {method: 'POST', body: data});
}
