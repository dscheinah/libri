import {fetchBackend} from './helper/fetch.js';

export async function list(account, search) {
    const params = new URLSearchParams();
    params.set('account', account || '');
    params.set('search', search || '');
    return fetchBackend('/ledger/list?' + params.toString());
}

export async function get(id) {
    const params = new URLSearchParams();
    params.set('id', id);
    return fetchBackend('/ledger/load?' + params.toString());
}

export async function assignable() {
    return fetchBackend('/ledger/list-assignable');
}

export async function save(data) {
    return fetchBackend('/ledger/save', {method: 'POST', body: data});
}

export async function cancel(data) {
    return fetchBackend('/ledger/remove', {method: 'POST', body: data});
}

export async function assign(data) {
    return fetchBackend('/ledger/save-assign', {method: 'POST', body: data});
}
