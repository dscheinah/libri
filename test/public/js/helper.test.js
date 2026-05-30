import * as helper from 'js/helper.js';

describe('helper exports', () => {
    test('should export all expected helpers', () => {
        expect(helper.create).toBeDefined();
        expect(helper.element).toBeDefined();
        expect(helper.elements).toBeDefined();
        expect(helper.focus).toBeDefined();
        expect(helper.list).toBeDefined();
        expect(helper.set).toBeDefined();
        expect(helper.style).toBeDefined();
    });
});
