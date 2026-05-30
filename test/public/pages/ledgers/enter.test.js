const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('ledgers-enter embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="ledgers-enter">
                <tbody id="ledgers-enter-list"></tbody>
                <button type="submit"></button>
            </form>
            <table id="ledgers-enter-template">
                <tr>
                    <td><input name="date[]"></td>
                    <td><select name="account[]"></select></td>
                    <td><select name="offset[]"></select></td>
                    <td><input name="amount[]"></td>
                    <td><input name="description[]"></td>
                    <td><input name="reference[]"></td>
                    <td><button data-delete></button></td>
                </tr>
            </table>
        `;
    });

    test('registers ledgers-enter page', () => {
        state.get.mockReturnValue({ length: 1 });

        loadHtmlScript('public/pages/ledgers/enter.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('ledgers-enter', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const actionMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, action: actionMock, listen: listenMock});
        
        expect(state.get).toHaveBeenCalledWith('ledgers-enter');
        expect(showMock).toHaveBeenCalled();
        expect(actionMock).toHaveBeenCalledWith('#ledgers-enter', 'input', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-enter [data-delete]', 'click', expect.any(Function));
        expect(actionMock).toHaveBeenCalledWith('#ledgers-enter', 'submit', expect.any(Function));
    });
});
