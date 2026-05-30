import {get, save} from 'js/repository/master.js';
import {fetchBackend} from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('master repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('get should call fetchBackend', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await get();
        expect(fetchBackend).toHaveBeenCalledWith('/master/load');
    });

    test('save should call fetchBackend with POST', async () => {
        const data = { foo: 'bar' };
        fetchBackend.mockResolvedValueOnce({});
        await save(data);
        expect(fetchBackend).toHaveBeenCalledWith('/master/save', { method: 'POST', body: data });
    });
});
