/**
 * A wrapper for the fetch API to communicate with the backend.
 * Handles JSON parsing and throws an error if the response is not ok.
 */
export async function fetchBackend(input, init) {
    const result = await fetch(input, init);
    if (result.ok) {
        if (result.status === 204) {
            return;
        }
        return result.json();
    }
    throw new Error(await result.text());
}
