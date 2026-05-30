const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('invoices-assign embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="invoices-assign-description"></div>
            <div id="invoices-assign-amount"></div>
            <form id="invoices-assign">
                <tbody></tbody>
                <button type="submit"></button>
            </form>
        `;
    });

    test('registers invoices-assign page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'invoice') return { id: 1, amount: 100, description: 'Test' };
            if (key === 'ledgers-assignable') return [];
            return null;
        });

        loadHtmlScript('public/pages/invoices/assign.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('invoices-assign', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('invoice');
        expect(state.get).toHaveBeenCalledWith('ledgers-assignable');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#invoices-assign', 'change', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-assign', 'submit', expect.any(Function));
    });
});
