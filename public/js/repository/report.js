import {fetchBackend} from './helper/fetch.js';

export async function dashboard() {
    return fetchBackend('/dashboard');
}
