import * as ledger from './ledger.js';
import * as invoice from './invoice.js';

export async function dashboard() {
    const ledgers = await ledger.list(), invoices = await invoice.list();
    return {
        accounts: ledgers.reduce((c, l) => c + l.amount, 0),
        categories: [
            {
                name: "Geschäftsbetrieb",
                amount: ledgers.reduce((c, l) => l.category === 'Geschäftsbetrieb' ? c + l.amount : c, 0),
            },
            {
                name: "Ideeller Bereich",
                amount: ledgers.reduce((c, l) => l.category === 'Ideeller Bereich' ? c + l.amount : c, 0),
            },
            {
                name: "Zweckbetrieb",
                amount: ledgers.reduce((c, l) => l.category === 'Zweckbetrieb' ? c + l.amount : c, 0),
            },
            {
                name: "ohne Zuordnung",
                amount: ledgers.reduce((c, l) => !l.category ? c + l.amount : c, 0),
            },
        ],
        problems: [
            {
                name: "Offene Buchungen",
                count: ledgers.filter((l) => !l.assigned && !l.canceled).length,
            },
            {
                name: "Offene Belege & Rechnungen",
                count: invoices.filter((i) => !i.assigned).length,
            },
            {
                name: "Fehlende Anhänge",
                count: invoices.filter((i) => i.type === 1 && !i.document && !i.no_document).length,
            },
        ]
    };
}
