import {fetchBackend} from './helper/fetch.js';

export async function list() {
    return fetchBackend('/account/list');
}

export async function real() {
    return fetchBackend('/account/list-real');
}

export async function save(data) {
    return fetchBackend('/account/save', {method: 'POST', body: data});
}
