export async function list() {
    return [
        {
            id: 1,
            name: 'Geschäftsbetrieb',
        },
        {
            id: 2,
            name: 'Ideeller Bereich',
        },
        {
            id: 3,
            name: 'Zweckbetrieb',
        },
    ];
}

export async function save(data) {
    console.debug('category.save', data);
}
