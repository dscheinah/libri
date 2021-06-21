const accounts = [
    {
        no: '1600',
        name: 'Kasse',
        category: null,
        real: true,
    },
    {
        no: '1800',
        name: 'Bank',
        category: null,
        real: true,
    },
    {
        no: '2000',
        name: 'Ideeller Bereich',
        category: 2,
        real: false,
    },
    {
        no: '6000',
        name: 'Zweckbetrieb',
        category: 3,
        real: false,
    },
    {
        no: '6010',
        name: 'Eintrittsgelder',
        category: 3,
        real: false,
    },
    {
        no: '8000',
        name: 'Geschäftsbetrieb',
        category: 1,
        real: false,
    },
    {
        no: '9000',
        name: '',
        category: null,
        real: false,
    }
];

export async function list() {
    return accounts;
}

export async function real() {
    return accounts.filter(account => account.real);
}
