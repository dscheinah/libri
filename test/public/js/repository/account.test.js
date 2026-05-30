import { list, real, save } from 'js/repository/account.js';
import { fetchBackend } from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('account repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('list should call fetchBackend with correct URL', async () => {
        const mockAccounts = [{ id: 1, name: 'Test' }];
        fetchBackend.mockResolvedValueOnce(mockAccounts);

        const result = await list();
        expect(result).toEqual(mockAccounts);
        expect(fetchBackend).toHaveBeenCalledWith('/account/list');
    });

    test('real should call fetchBackend with correct URL', async () => {
        const mockAccounts = [{ id: 2, name: 'Real' }];
        fetchBackend.mockResolvedValueOnce(mockAccounts);

        const result = await real();
        expect(result).toEqual(mockAccounts);
        expect(fetchBackend).toHaveBeenCalledWith('/account/list-real');
    });

    test('save should call fetchBackend with correct URL and options', async () => {
        const mockResult = { success: true };
        const data = { name: 'New Account' };
        fetchBackend.mockResolvedValueOnce(mockResult);

        const result = await save(data);
        expect(result).toEqual(mockResult);
        expect(fetchBackend).toHaveBeenCalledWith('/account/save', { method: 'POST', body: data });
    });
});
