const { loadHtmlScript } = require('../utils.js');
const {helper, page, state} = require('js/app.js');

describe('contacts embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="contacts">
                <input name="search" value="">
                <tbody></tbody>
            </form>
        `;
    });

    test('registers contacts page and handles actions', () => {
        loadHtmlScript('public/pages/contacts.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('contacts', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        // Initial state load
        expect(state.get).toHaveBeenCalledWith('contacts');
        
        // Show handler
        expect(showMock).toHaveBeenCalled();
        const loadFunc = showMock.mock.calls[0][0];
        
        // Mock helper.element for the load function
        helper.element.mockReturnValue({ search: { value: 'test-search' } });
        loadFunc();
        expect(state.dispatch).toHaveBeenCalledWith('contacts', 'test-search');
        
        // Action handlers
        expect(actionMock).toHaveBeenCalledWith('#contacts', 'submit', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#contacts [data-add]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#contacts [data-contact]', 'click', expect.any(Function));
    });
});
