const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('contacts-details embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <div id="contacts-details">
                <td id="contacts-details-name"></td>
                <td id="contacts-details-mail"></td>
                <td id="contacts-details-phone"></td>
                <td id="contacts-details-address"></td>
                <td id="contacts-details-income"></td>
                <td id="contacts-details-expense"></td>
                <button data-invoice></button>
                <button data-edit></button>
                <button data-delete></button>
            </div>
        `;
        window.confirm = jest.fn(() => true);
    });

    test('registers contacts-details page', () => {
        state.get.mockReturnValue({ id: 1, name: 'John Doe', income: 100, expense: 50 });

        loadHtmlScript('public/pages/contacts/details.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('contacts-details', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('contact');
        expect(actionMock).toHaveBeenCalledWith('#contacts-details [data-invoice]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#contacts-details [data-edit]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#contacts-details [data-delete]', 'click', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact-save', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact-delete', expect.any(Function));
    });

    test('handles delete confirmation', () => {
        state.get.mockReturnValue({ id: 1, name: 'John Doe' });
        loadHtmlScript('public/pages/contacts/details.html', { helper, page, state });
        const registerCallback = page.register.mock.calls[0][1];
        
        let deleteAction;
        const actionMock = (selector, event, callback) => {
            if (selector === '#contacts-details [data-delete]') deleteAction = callback;
        };
        
        registerCallback({render: jest.fn(), action: actionMock, listen: jest.fn()});
        
        deleteAction();
        expect(window.confirm).toHaveBeenCalled();
        expect(state.dispatch).toHaveBeenCalledWith('contact-delete', 1);
    });
});
