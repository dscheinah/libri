import {fetchBackend} from './helper/fetch.js';

export async function get() {
    return fetchBackend('/master/load');
}

export async function save(data) {
    return fetchBackend('/master/save', {method: 'POST', body: data});
}
