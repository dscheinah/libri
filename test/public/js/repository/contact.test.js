import { list, get, save, remove } from 'js/repository/contact.js';
import { fetchBackend } from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('contact repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('list should call fetchBackend with search parameter', async () => {
        const mockData = [{ id: 1, name: 'Contact' }];
        fetchBackend.mockResolvedValueOnce(mockData);

        const result = await list('test');
        expect(result).toEqual(mockData);
        expect(fetchBackend).toHaveBeenCalledWith('/contact/list?search=test');
    });

    test('list should work without search parameter', async () => {
        fetchBackend.mockResolvedValueOnce([]);
        await list();
        expect(fetchBackend).toHaveBeenCalledWith('/contact/list?search=');
    });

    test('get should call fetchBackend with ID', async () => {
        const mockData = { id: 1, name: 'Contact' };
        fetchBackend.mockResolvedValueOnce(mockData);

        const result = await get(1);
        expect(result).toEqual(mockData);
        expect(fetchBackend).toHaveBeenCalledWith('/contact/load?id=1');
    });

    test('save should call fetchBackend with POST', async () => {
        const mockResult = { success: true };
        const data = { name: 'New Contact' };
        fetchBackend.mockResolvedValueOnce(mockResult);

        const result = await save(data);
        expect(result).toEqual(mockResult);
        expect(fetchBackend).toHaveBeenCalledWith('/contact/save', { method: 'POST', body: data });
    });

    test('remove should call fetchBackend with DELETE and ID', async () => {
        const mockResult = { success: true };
        fetchBackend.mockResolvedValueOnce(mockResult);

        const result = await remove(1);
        expect(result).toEqual(mockResult);
        expect(fetchBackend).toHaveBeenCalledWith('/contact/remove?id=1', { method: 'DELETE' });
    });
});
