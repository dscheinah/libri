const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('settings/accounts embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="settings-accounts">
                <form id="settings-accounts"></form>
                <button data-add></button>
                <button data-delete></button>
            </div>
        `;
        global.history.back = jest.fn();
    });

    test('registers settings-accounts page', () => {
        loadHtmlScript('public/pages/settings/accounts.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('settings-accounts', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(actionMock).toHaveBeenCalledWith('#settings-accounts [data-delete]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#settings-accounts [data-add]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#settings-accounts', 'submit', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('accounts', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('categories', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('accounts-save', expect.any(Function));
    });
});
