const { loadHtmlScript } = require('../utils.js');
const {helper, page, state} = require('js/app.js');

describe('dashboard embedded script', () => {
    beforeEach(() => {
        jest.resetAllMocks();
        document.body.innerHTML = '<div id="dashboard-accounts"></div><table id="dashboard-categories"></table><table id="dashboard-problems"></table>';
    });

    test('registers dashboard page and handles data', () => {
        loadHtmlScript('public/pages/dashboard.html', { helper, page, state });
        
        expect(page.register).toHaveBeenCalledWith('dashboard', expect.any(Function));
        
        // Teste die Registrierungs-Logik
        const registerCallback = page.register.mock.calls[0][1];
        const renderMock = jest.fn();
        const showMock = jest.fn();
        const listenMock = jest.fn();
        
        registerCallback({render: renderMock, show: showMock, listen: listenMock});
        
        // Überprüfe initialen State-Abruf
        expect(state.get).toHaveBeenCalledWith('dashboard');
        
        // Überprüfe show-Handler
        expect(showMock).toHaveBeenCalledWith(expect.any(Function));
        showMock.mock.calls[0][0]();
        expect(state.dispatch).toHaveBeenCalledWith('dashboard', null);
        
        // Überprüfe listen-Handler
        expect(listenMock).toHaveBeenCalledWith('dashboard', expect.any(Function));
    });
});
