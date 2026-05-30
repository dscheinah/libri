const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('ledgers-details embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <table id="ledgers-details">
                <tr id="ledgers-details-assignment">
                    <ul id="ledgers-details-assigned"></ul>
                    <span id="ledgers-details-assignable"></span>
                    <button id="ledgers-details-assign"></button>
                </tr>
                <tr id="ledgers-details-transfer"></tr>
                <td id="ledgers-details-id"></td>
                <td id="ledgers-details-date"></td>
                <td id="ledgers-details-account"></td>
                <td id="ledgers-details-offset"></td>
                <td id="ledgers-details-description"></td>
                <td id="ledgers-details-amount"></td>
                <td id="ledgers-details-reference"></td>
            </table>
            <form id="ledgers-details-cancel">
                <input name="reason">
                <button></button>
            </form>
        `;
    });

    test('registers ledgers-details page', () => {
        state.get.mockReturnValue({ invoices: [] });

        loadHtmlScript('public/pages/ledgers/details.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('ledgers-details', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('ledger');
        expect(actionMock).toHaveBeenCalledWith('#ledgers-details [data-invoice]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-details-cancel', 'submit', expect.any(Function));
    });
});
