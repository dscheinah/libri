import {fetchBackend} from './helper/fetch.js';

export async function list() {
    return fetchBackend('/category/list');
}

export async function save(data) {
    return fetchBackend('/category/save', {method: 'POST', body: data});
}
