import {list, save} from 'js/repository/category.js';
import {fetchBackend} from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('category repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('list should call fetchBackend with correct URL', async () => {
        const mockData = [{ id: 1, name: 'Category' }];
        fetchBackend.mockResolvedValueOnce(mockData);

        const result = await list();
        expect(result).toEqual(mockData);
        expect(fetchBackend).toHaveBeenCalledWith('/category/list');
    });

    test('save should call fetchBackend with correct URL and options', async () => {
        const mockResult = { success: true };
        const data = { name: 'New Category' };
        fetchBackend.mockResolvedValueOnce(mockResult);

        const result = await save(data);
        expect(result).toEqual(mockResult);
        expect(fetchBackend).toHaveBeenCalledWith('/category/save', { method: 'POST', body: data });
    });
});
