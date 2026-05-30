const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('settings-categories embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="settings-categories">
                <tbody></tbody>
                <button type="submit"></button>
                <button type="button" data-add></button>
            </form>
        `;
    });

    test('registers settings-categories page', () => {
        state.get.mockReturnValue([]);

        loadHtmlScript('public/pages/settings/categories.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('settings-categories', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('categories');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#settings-categories [data-delete]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#settings-categories [data-add]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#settings-categories', 'submit', expect.any(Function));
    });
});
