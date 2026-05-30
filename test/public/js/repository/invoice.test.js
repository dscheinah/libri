import { list, get, assignable, save, finish, remove, assign } from 'js/repository/invoice.js';
import { fetchBackend } from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('invoice repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('list should call fetchBackend with type and search', async () => {
        fetchBackend.mockResolvedValueOnce([]);
        await list(1, 'test');
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/list?type=1&search=test');
    });

    test('get should call fetchBackend with ID', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await get(123);
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/load?id=123');
    });

    test('assignable should call fetchBackend', async () => {
        fetchBackend.mockResolvedValueOnce([]);
        await assignable();
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/list-assignable');
    });

    test('save should call fetchBackend with POST', async () => {
        const data = { foo: 'bar' };
        fetchBackend.mockResolvedValueOnce({});
        await save(data);
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/save', { method: 'POST', body: data });
    });

    test('finish should call fetchBackend with PUT', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await finish(456);
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/finish?id=456', { method: 'PUT' });
    });

    test('remove should call fetchBackend with DELETE', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await remove(789);
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/remove?id=789', { method: 'DELETE' });
    });

    test('assign should call fetchBackend with POST', async () => {
        const data = { invoiceId: 1, ledgerIds: [1, 2] };
        fetchBackend.mockResolvedValueOnce({});
        await assign(data);
        expect(fetchBackend).toHaveBeenCalledWith('/invoice/save-assign', { method: 'POST', body: data });
    });
});
