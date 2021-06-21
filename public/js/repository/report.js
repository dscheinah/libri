import * as ledger from './ledger.js';
import * as invoice from './invoice.js';

export async function dashboard() {
    const ledgers = await ledger.list(), invoices = await invoice.list();
    const reducer = (c, l) => c + l.amount;
    return {
        accounts: ledgers.reduce(reducer, 0)
            - ledgers.filter((l) => ['1600', '1800'].includes(l.offset.no)).reduce(reducer, 0),
        categories: [
            {
                name: "Geschäftsbetrieb",
                amount: ledgers.reduce((c, l) => l.offset.no === '8000' ? c + l.amount : c, 0),
            },
            {
                name: "Ideeller Bereich",
                amount: ledgers.reduce((c, l) => l.offset.no === '2000' ? c + l.amount : c, 0),
            },
            {
                name: "Zweckbetrieb",
                amount: ledgers.reduce((c, l) => ['6000', '6010'].includes(l.offset.no) ? c + l.amount : c, 0),
            },
            {
                name: "ohne Zuordnung",
                amount: ledgers.reduce((c, l) => l.offset.no === '9000' ? c + l.amount : c, 0),
            },
        ],
        problems: [
            {
                name: "Buchungen ohne Beleg",
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
