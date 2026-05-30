import {fetchBackend} from 'js/repository/helper/fetch.js';

describe('fetchBackend', () => {
    beforeEach(() => {
        global.fetch = jest.fn();
    });

    afterEach(() => {
        jest.restoreAllMocks();
    });

    test('should return json if response is ok', async () => {
        const mockData = { foo: 'bar' };
        global.fetch.mockResolvedValueOnce({
            ok: true,
            status: 200,
            json: jest.fn().mockResolvedValueOnce(mockData)
        });

        const result = await fetchBackend('/api/test');
        expect(result).toEqual(mockData);
        expect(global.fetch).toHaveBeenCalledWith('/api/test', undefined);
    });

    test('should return undefined if response status is 204', async () => {
        global.fetch.mockResolvedValueOnce({
            ok: true,
            status: 204
        });

        const result = await fetchBackend('/api/test');
        expect(result).toBeUndefined();
    });

    test('should throw error if response is not ok', async () => {
        const errorMessage = 'Not Found';
        global.fetch.mockResolvedValueOnce({
            ok: false,
            status: 404,
            text: jest.fn().mockResolvedValueOnce(errorMessage)
        });

        await expect(fetchBackend('/api/test')).rejects.toThrow(errorMessage);
    });
});
