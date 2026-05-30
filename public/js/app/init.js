/**
 * Populates the initial application state.
 * Loads previously entered ledger entries from localStorage if available.
 *
 * @param {State} state
 */
export default function (state) {
    // This can also be loaded from e.g. localStorage or a backend.
    state.set('backend-data', ['initial data']);

    let storedLedgerEntries = localStorage.getItem('ledgers-enter');
    if (storedLedgerEntries) {
        state.set('ledgers-enter', JSON.parse(storedLedgerEntries));
    }
}
