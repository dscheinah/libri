import {fetchBackend} from './helper/fetch.js';

export async function list(search) {
    const params = new URLSearchParams();
    params.set('search', search || '');
    return fetchBackend('/contact/list?' + params.toString());
}

export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/contact/load?' + params.toString());
}

export async function save(data) {
    return fetchBackend('/contact/save', {method: 'POST', body: data});
}

export async function remove(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/contact/remove?' + params.toString(), {method: 'DELETE'});
}
