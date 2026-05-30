const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('contacts-edit embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <span id="contacts-edit-type"></span>
            <input id="contacts-edit-name" name="name">
            <input id="contacts-edit-mail" name="mail">
            <input id="contacts-edit-phone" name="phone">
            <textarea id="contacts-edit-address" name="address"></textarea>
            <form id="contacts-edit"></form>
        `;
        global.history.back = jest.fn();
    });

    test('registers contacts-edit page', () => {
        state.get.mockImplementation((key) => {
            if (key === 'contact') return { id: 1, name: 'John Doe' };
            if (key === 'contact-edit') return true;
            return null;
        });

        loadHtmlScript('public/pages/contacts/edit.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('contacts-edit', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('contact');
        expect(state.get).toHaveBeenCalledWith('contact-edit');
        expect(actionMock).toHaveBeenCalledWith('#contacts-edit', 'submit', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact-edit', expect.any(Function));
        expect(listenMock).toHaveBeenCalledWith('contact-save', expect.any(Function));
    });

    test('handles form submission', () => {
        state.get.mockImplementation((key) => {
            if (key === 'contact') return { id: 123 };
            if (key === 'contact-edit') return true;
            return null;
        });
        loadHtmlScript('public/pages/contacts/edit.html', { helper, page, state });
        const registerCallback = page.register.mock.calls[0][1];
        
        let submitCallback;
        const actionMock = (selector, event, callback) => {
            if (selector === '#contacts-edit' && event === 'submit') submitCallback = callback;
        };
        
        registerCallback({render: jest.fn(), action: actionMock, listen: jest.fn()});
        
        const preventDefault = jest.fn();
        const target = document.createElement('form');
        submitCallback({ preventDefault, target });
        
        expect(preventDefault).toHaveBeenCalled();
        expect(state.dispatch).toHaveBeenCalledWith('contact-save', expect.any(FormData));
    });
});
