const { loadHtmlScript } = require('../utils.js');
const {helper, page, state} = require('js/app.js');

describe('ledgers embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="ledgers">
                <div id="ledgers-accounts"></div>
                <tbody id="ledgers-list"></tbody>
                <input name="search" value="">
            </form>
        `;
    });

    test('registers ledgers page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'accounts-real') return [];
            if (key === 'ledgers') return [];
            if (key === 'ledgers-account') return null;
            return null;
        });

        loadHtmlScript('public/pages/ledgers.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('ledgers', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('accounts-real');
        expect(state.get).toHaveBeenCalledWith('ledgers');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#ledgers [name=account]', 'input', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers', 'submit', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers [data-details]', 'click', expect.any(Function));
    });
});
