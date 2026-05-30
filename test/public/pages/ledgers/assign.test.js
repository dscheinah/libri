const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('ledgers-assign embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="ledgers-assign-description"></div>
            <div id="ledgers-assign-amount"></div>
            <form id="ledgers-assign">
                <tbody></tbody>
                <button type="submit"></button>
                <button type="button" data-invoice></button>
            </form>
        `;
    });

    test('registers ledgers-assign page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'ledger') return { id: 1, amount: 100, description: 'Test' };
            if (key === 'invoices-assignable') return [];
            return null;
        });

        loadHtmlScript('public/pages/ledgers/assign.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('ledgers-assign', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('ledger');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#ledgers-assign', 'change', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-assign', 'submit', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-assign [data-invoice]', 'click', expect.any(Function));
    });
});
