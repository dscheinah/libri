// Wir verwenden jest.isolateModules, um app.js in einer sauberen Umgebung zu testen,
// da es Seiteneffekte beim Import hat (window.sxAppInitialized).
describe('app.js', () => {
    beforeEach(() => {
        jest.resetModules();
        delete window.sxAppInitialized;
        document.body.innerHTML = '<div id="main"></div><div id="loading"></div>';
        
        // Den globalen Mock in setup.js für diesen Test aufheben
        jest.unmock('js/app.js');
    });

    test('Initialisierung von app.js', () => {
        // Wir können prüfen, ob sxAppInitialized gesetzt wird
        require('js/app.js');
        expect(window.sxAppInitialized).toBe(true);
    });

    test('Verhindert mehrfache Initialisierung', () => {
        window.sxAppInitialized = true;
        expect(() => {
            require('js/app.js');
        }).toThrow('Tried to access a cached app. Please reload the page.');
    });

    test('Registriert Handler im State', () => {
        // Da app.js intern new State() aufruft, und State gemockt ist,
        // können wir die Instanz über den Mock-Konstruktor abgreifen.
        require('js/app.js');
        
        const StateMock = require('vendor/dscheinah/sx-js/src/State.js').default;
        const stateInstance = StateMock.mock.instances[0];
        
        // Prüfen, ob wichtige Handler registriert wurden
        const handledKeys = stateInstance.handle.mock.calls.map(call => call[0]);
        expect(handledKeys).toContain('accounts');
        expect(handledKeys).toContain('contacts');
        expect(handledKeys).toContain('dashboard');
        expect(handledKeys).toContain('loading');
    });

    test('Registriert Seiten im Page-Manager', () => {
        require('js/app.js');
        
        const PageMock = require('vendor/dscheinah/sx-js/src/Page.js').default;
        const pageInstance = PageMock.mock.instances[0];
        
        // Prüfen, ob Seiten hinzugefügt wurden
        expect(pageInstance.add).toHaveBeenCalled();
        expect(pageInstance.show).toHaveBeenCalledWith('dashboard');
    });
});
