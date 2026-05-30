const { loadHtmlScript } = require('../utils.js');
const {helper, page, state} = require('js/app.js');

describe('home embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = '<div id="home"><span id="home-date"></span></div>';
    });

    test('registers home page', () => {
        loadHtmlScript('public/pages/home.html', { helper, page, state });
        expect(page.register).toHaveBeenCalledWith('home', expect.any(Function));
    });
});
