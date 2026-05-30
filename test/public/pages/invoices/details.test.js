const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('invoices-details embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="invoices-details">
                <h1></h1>
                <span id="invoices-details-assignable"></span>
                <ul id="invoices-details-assigned"></ul>
                <button id="invoices-details-assign"></button>
                <td id="invoices-details-id"></td>
                <td id="invoices-details-date"></td>
                <td id="invoices-details-amount"></td>
                <td id="invoices-details-description"></td>
                <td id="invoices-details-reference"></td>
                <span id="invoices-details-no-document"></span>
                <a id="invoices-details-document"></a>
                <tr id="invoices-details-contact">
                    <td id="invoices-details-contact-address"></td>
                    <button id="invoices-details-contact-id"></button>
                </tr>
                <button data-edit></button>
                <button id="invoices-details-finish"></button>
                <button id="invoices-details-delete"></button>
            </div>
        `;
    });

    test('registers invoices-details page', () => {
        state.get.mockReturnValue({ type: 1, ledgers: [] });

        loadHtmlScript('public/pages/invoices/details.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('invoices-details', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('invoice');
        expect(actionMock).toHaveBeenCalledWith('#invoices-details [data-ledger]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-details [data-contact]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-details [data-edit]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-details-finish', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-details-delete', 'click', expect.any(Function));
    });
});
