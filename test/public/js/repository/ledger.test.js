import { list, get, assignable, save, cancel, assign } from 'js/repository/ledger.js';
import { fetchBackend } from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('ledger repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('list should call fetchBackend with account and search', async () => {
        fetchBackend.mockResolvedValueOnce([]);
        await list('123', 'test');
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/list?account=123&search=test');
    });

    test('get should call fetchBackend with ID', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await get(1);
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/load?id=1');
    });

    test('assignable should call fetchBackend', async () => {
        fetchBackend.mockResolvedValueOnce([]);
        await assignable();
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/list-assignable');
    });

    test('save should call fetchBackend with POST', async () => {
        const data = { foo: 'bar' };
        fetchBackend.mockResolvedValueOnce({});
        await save(data);
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/save', { method: 'POST', body: data });
    });

    test('cancel should call fetchBackend with POST to remove', async () => {
        const data = { id: 1, reason: 'test' };
        fetchBackend.mockResolvedValueOnce({});
        await cancel(data);
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/remove', { method: 'POST', body: data });
    });

    test('assign should call fetchBackend with POST', async () => {
        const data = { ledgerId: 1, invoiceIds: [1, 2] };
        fetchBackend.mockResolvedValueOnce({});
        await assign(data);
        expect(fetchBackend).toHaveBeenCalledWith('/ledger/save-assign', { method: 'POST', body: data });
    });
});
