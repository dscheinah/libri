const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('ledgers-import embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="ledgers-import">
                <textarea id="ledgers-import-data" name="data"></textarea>
                <input type="text" id="ledgers-import-divider" name="divider">
                <select id="ledgers-import-account" name="account"></select>
                <div id="ledgers-import-parsed"></div>
                <button type="submit"></button>
            </form>
        `;
    });

    test('registers ledgers-import page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'ledgers-import') return { data: '', divider: ';', account: null, parsed: [] };
            if (key === 'accounts-real') return [];
            return null;
        });

        loadHtmlScript('public/pages/ledgers/import.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('ledgers-import', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('ledgers-import');
        expect(state.get).toHaveBeenCalledWith('accounts-real');
        expect(actionMock).toHaveBeenCalledWith('#ledgers-import', 'change', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-import', 'submit', expect.any(Function));
    });
});
