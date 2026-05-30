import { dashboard } from 'js/repository/report.js';
import { fetchBackend } from 'js/repository/helper/fetch.js';

jest.mock('js/repository/helper/fetch.js');

describe('report repository', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    test('dashboard should call fetchBackend', async () => {
        fetchBackend.mockResolvedValueOnce({});
        await dashboard();
        expect(fetchBackend).toHaveBeenCalledWith('/dashboard');
    });
});
