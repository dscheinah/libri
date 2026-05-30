const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('settings-master embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="settings-master">
                <form>
                    <textarea id="settings-master-address" name="address"></textarea>
                    <textarea id="settings-master-account" name="account"></textarea>
                    <input type="text" id="settings-master-number" name="number"/>
                    <button type="submit"></button>
                </form>
            </div>
        `;
    });

    test('registers settings-master page', () => {
        state.get.mockReturnValue({});

        loadHtmlScript('public/pages/settings/master.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('settings-master', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('master');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#settings-master form', 'submit', expect.any(Function));
    });
});
