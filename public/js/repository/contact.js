import * as invoice from './invoice.js';

const contacts = [
    {
        "id": 1,
        "name": "Marvin Dennis",
        "mail": "a.magna@ullamcorper.org",
        "phone": "(032360) 530107",
        "address": "Marvin Dennis\n7981 Commodo Road\n05903 Neiva",
    },
    {
        "id": 2,
        "name": "Linus Zimmerman",
        "mail": "curabitur.vel@morbi.com",
        "phone": "",
        "address": "Linus Zimmerman\n259-7856 Nisl Av.\n59756 Roccabruna",
    },
    {
        "id": 3,
        "name": "Octavia Hampton",
        "mail": "",
        "phone": "(0494) 01977234",
        "address": "Octavia Hampton\n784-5943 Id, St.\n53845 Schulen",
    },
    {
        "id": 4,
        "name": "Jack Chavez",
        "mail": "",
        "phone": "",
        "address": "",
    },
];

export async function list(search) {
    if (search) {
        search = search.toLowerCase();
        return contacts.filter((contact) => contact.name.toLowerCase().includes(search));
    }
    return contacts;
}

export async function get(id) {
    const contact = contacts[id - 1];
    contact.income = 0.0;
    contact.expense = 0.0;
    const invoices = await invoice.list();
    invoices.forEach((invoice) => {
        if (!invoice.contact || invoice.contact.id !== id) {
            return;
        }
        if (invoice.amount < 0) {
            contact.expense += invoice.amount;
        } else {
            contact.income += invoice.amount;
        }
    })
    return contact;
}
