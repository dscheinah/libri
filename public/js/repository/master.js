export async function get() {
    return {
        address: "Cecilia Becker\nP.O. Box 568, 3980 Lectus, Rd.\n27645 İmamoğlu",
        account: "DE63 0432 7861 0112 9837",
        number: "R#nummer#-#jahr#-#checksum#",
    };
}

export async function save(data) {
    console.debug('master.save', data);
}
