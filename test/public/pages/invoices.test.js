const { loadHtmlScript } = require('../utils.js');
const {helper, page, state} = require('js/app.js');

describe('invoices embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="invoices">
                <input name="type" value="1" checked>
                <input name="search" value="">
                <tbody></tbody>
            </form>
        `;
    });

    test('registers invoices page and handles actions', () => {
        loadHtmlScript('public/pages/invoices.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('invoices', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        // Initial state load
        expect(state.get).toHaveBeenCalledWith('invoices');
        
        // Show handler
        expect(showMock).toHaveBeenCalled();
        const loadFunc = showMock.mock.calls[0][0];
        
        // Mock helper.element for the load function
        helper.element.mockReturnValue({
            type: { value: '1' },
            search: { value: '' }
        });
        loadFunc();
        expect(state.dispatch).toHaveBeenCalledWith('invoices', { type: 1, search: '' });
        
        // Action handlers
        expect(actionMock).toHaveBeenCalledWith('#invoices [name=type]', 'input', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices', 'submit', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices [data-add]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices [data-details]', 'click', expect.any(Function));
    });
});
