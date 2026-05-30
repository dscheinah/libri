const fs = require('fs');
const path = require('path');

/**
 * Loads and executes scripts from an HTML file within the current JSDOM environment.
 * 
 * @param {string} htmlPath Path to the HTML file relative to the project root.
 * @param {Object} context Object containing variables to be made available in the script's scope.
 */
function loadHtmlScript(htmlPath, context = {}) {
    const fullPath = path.resolve(__dirname, '../../', htmlPath);
    const htmlContent = fs.readFileSync(fullPath, 'utf8');
    
    const scriptMatch = htmlContent.match(/<script\b[^>]*>([\s\S]*?)<\/script>/);
    if (!scriptMatch) {
        throw new Error(`No script tag found in ${htmlPath}`);
    }
    
    let scriptContent = scriptMatch[1].trim();
    
    // Remove imports from app.js as we provide them via context
    scriptContent = scriptContent.replace(/import\s+\{.*?\}\s+from\s+['"].*?js\/app\.js['"];?/g, '');
    
    // Expose context to global scope for eval
    Object.assign(global, context);
    
    return eval(scriptContent);
}

module.exports = { loadHtmlScript };
