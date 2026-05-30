import {fetchBackend} from './helper/fetch.js';

/**
 * Fetches dashboard data from the backend.
 *
 * @returns {Promise<Object>}
 */
export async function dashboard() {
    return fetchBackend('/dashboard');
}
