const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('reports-accounts embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="reports-accounts">
                <select id="reports-accounts-accounts" name="accounts[]" multiple></select>
                <button type="submit"></button>
            </form>
        `;
    });

    test('registers reports-accounts page', () => {
        state.get.mockReturnValue([]);

        loadHtmlScript('public/pages/reports/accounts.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('reports-accounts', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('accounts');
        expect(showMock).toHaveBeenCalled();
        expect(listenMock).toHaveBeenCalledWith('accounts', expect.any(Function));
    });
});
