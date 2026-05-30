const { loadHtmlScript } = require('../../utils.js');
const {helper, page, state} = require('js/app.js');

describe('reports-categories embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = `
            <form id="reports-categories">
                <select id="reports-categories-categories" name="categories[]" multiple></select>
                <button type="submit"></button>
            </form>
        `;
    });

    test('registers reports-categories page', () => {
        loadHtmlScript('public/pages/reports/categories.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('reports-categories', expect.any(Function));
        
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, listen: listenMock});
        
        expect(showMock).toHaveBeenCalled();
        expect(listenMock).toHaveBeenCalledWith('categories', expect.any(Function));
    });
});
