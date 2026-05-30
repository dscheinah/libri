const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('invoices-edit embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="invoices-edit">
                <h1></h1>
                <div id="invoices-edit-type">
                    <input type="radio" name="type" value="1">
                    <input type="radio" name="type" value="2">
                </div>
                <input type="date" id="invoices-edit-date" name="date">
                <input type="number" id="invoices-edit-amount" name="amount">
                <input type="text" id="invoices-edit-description" name="description">
                <div id="invoices-edit-document">
                    <input type="text" id="invoices-edit-reference" name="reference">
                    <input type="file" id="invoices-edit-file" name="document">
                    <input type="checkbox" id="invoices-edit-no-document" name="no_document">
                </div>
                <select id="invoices-edit-contact-id" name="contact_id"></select>
                <textarea id="invoices-edit-contact-address" name="contact_address"></textarea>
                <button type="submit"></button>
            </form>
        `;
    });

    test('registers invoices-edit page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'invoice') return {};
            if (key === 'invoice-preset') return {};
            if (key === 'invoice-edit') return false;
            if (key === 'invoice-type') return 1;
            if (key === 'invoice-contact') return null;
            if (key === 'contacts') return [];
            return null;
        });

        loadHtmlScript('public/pages/invoices/edit.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('invoices-edit', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('invoice');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#invoices-edit [name=type]', 'input', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#invoices-edit', 'submit', expect.any(Function));
    });
});
