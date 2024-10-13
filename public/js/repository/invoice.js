import {fetchBackend} from './helper/fetch.js';

export async function list(type, search) {
    const params = new URLSearchParams();
    params.set('type', type || '');
    params.set('search', search || '');
    return fetchBackend('/invoice/list?' + params.toString());
}

export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/invoice/load?' + params.toString());
}

export async function assignable() {
    return fetchBackend('/invoice/list-assignable');
}

export async function save(data) {
    return fetchBackend('/invoice/save', {method: 'POST', body: data});
}

export async function remove(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/invoice/remove?' + params.toString(), {method: 'DELETE'});
}

export async function assign(data) {
    return fetchBackend('/invoice/save-assign', {method: 'POST', body: data});
}
