const ledgers = [
    {
        "id": 1,
        "date": "2020-12-31",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "9000",
            "description": "9000",
        },
        "description": "non lacinia at iaculis quis",
        "amount": 240.86,
        "assigned": true,
        "invoices": [{id: 1, description: 'erat Vivamus nisi Mauris'}],
        "reference": "16741106 8757",
        "canceled": false,
    },
    {
        "id": 2,
        "date": "2021-01-13",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "8000",
            "description": "8000 - Geschäftsbetrieb",
        },
        "description": "erat semper rutrum Fusce",
        "amount": 389.60,
        "assigned": true,
        "invoices": [{id: 7, description: 'Proin dolor Nulla semper'}],
        "reference": "16331223 5900",
        "canceled": false,
    },
    {
        "id": 3,
        "date": "2021-01-24",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "2000",
            "description": "2000 - Ideeller Bereich",
        },
        "description": "Phasellus libero mauris aliquam eu",
        "amount": -302.32,
        "assigned": false,
        "invoices": [],
        "reference": "16620705 6984",
        "canceled": false,
    },
    {
        "id": 4,
        "date": "2021-02-03",
        "account": {
            "no": "1600",
            "description": "1600 - Kasse",
        },
        "offset": {
            "no": "6010",
            "description": "6010 - Eintrittsgelder",
        },
        "description": "facilisis vitae orci Phasellus",
        "amount": 238.60,
        "assigned": true,
        "invoices": [{id: 8, description: 'scelerisque mollis'}],
        "reference": "16011116 4281",
        "canceled": false,
    },
    {
        "id": 5,
        "date": "2021-02-14",
        "account": {
            "no": "1600",
            "description": "1600 - Kasse",
        },
        "offset": {
            "no": "2000",
            "description": "2000 - Ideeller Bereich",
        },
        "description": "amet risus Donec",
        "amount": 146.22,
        "assigned": false,
        "invoices": [],
        "reference": "16311011 3465",
        "canceled": false,
    },
    {
        "id": 6,
        "date": "2021-02-25",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "8000",
            "description": "8000 - Geschäftsbetrieb",
        },
        "description": "Nam nulla magna malesuada vel",
        "amount": -100.68,
        "assigned": false,
        "invoices": [],
        "reference": "16460117 1376",
        "canceled": false,
    },
    {
        "id": 7,
        "date": "2021-03-04",
        "account": {
            "no": "1600",
            "description": "1600 - Kasse",
        },
        "offset": {
            "no": "2000",
            "description": "2000 - Ideeller Bereich",
        },
        "description": "malesuada vel convallis",
        "amount": -224.48,
        "assigned": false,
        "invoices": [],
        "reference": "16690730 0427",
        "canceled": true,
    },
    {
        "id": 8,
        "date": "2021-03-04",
        "account": {
            "no": "1600",
            "description": "1600 - Kasse",
        },
        "offset": {
            "no": "2000",
            "description": "2000 - Ideeller Bereich",
        },
        "description": "malesuada vel convallis",
        "amount": -24.48,
        "assigned": true,
        "invoices": [{id: 4, description: 'egestas Fusce'}],
        "reference": "16690730 0427",
        "canceled": false,
    },
    {
        "id": 9,
        "date": "2021-03-15",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "6000",
            "description": "6000 - Zweckbetrieb",
        },
        "description": "venenatis vel faucibus id",
        "amount": -86.50,
        "assigned": true,
        "invoices": [{id: 4, description: 'egestas Fusce'}],
        "reference": "16470507 1324",
        "canceled": false,
    },
    {
        "id": 10,
        "date": "2021-03-26",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "2000",
            "description": "2000 - Ideeller Bereich",
        },
        "description": "ullamcorper Duis cursus diam at",
        "amount": 217.56,
        "assigned": true,
        "invoices": [{id: 5, description: 'nunc feugiat Sed nec'}, {id: 6, description: 'Quisque fringilla euismod'}],
        "reference": "16510328 0920",
        "canceled": false,
    },
    {
        "id": 11,
        "date": "2021-04-01",
        "account": {
            "no": "1800",
            "description": "1800 - Bank",
        },
        "offset": {
            "no": "1600",
            "description": "1600 - Kasse",
        },
        "description": "faucibus ullamcorper rutrum vel",
        "amount": 52.12,
        "assigned": false,
        "invoices": [],
        "reference": "",
        "canceled": false,
    },
];

export async function list(account, search) {
    if (search) {
        search = search.toLowerCase();
    }
    return ledgers.filter((ledger) => {
       if (account) {
           return ledger.account.no === account;
       }
       if (search) {
           return ledger.description.toLowerCase().includes(search)
               || ledger.reference.toLowerCase().includes(search)
               || ('' + ledger.id).includes(search);
       }
       return true;
    }).reverse();
}

export async function get(id) {
    return ledgers[id - 1];
}

export async function assignable() {
    return ledgers.filter((ledger) => !ledger.assigned && !ledger.canceled);
}
