<?php

namespace App\Repository;

use App\Storage\ContactStorage;

/**
 * Use this repository to handle contact management (CRUD).
 */
class ContactRepository
{
    public function __construct(
        private readonly ContactStorage $storage,
    ) {
    }

    /**
     * Retrieves a list of contacts.
     * Use this method to get all contacts or search for specific ones by name or other attributes.
     *
     * @param string $search Search term to filter contacts.
     *
     * @return list<mixed> A list of contact data.
     */
    public function listContacts(string $search): array
    {
        $contacts = $search ? $this->storage->fetchSome($search) : $this->storage->fetchAll();
        return iterator_to_array($contacts);
    }

    /**
     * Retrieves a single contact by its ID.
     * Use this method to get detailed information about a contact for editing or display.
     *
     * @param int $id The unique identifier of the contact.
     *
     * @return array<string, mixed>|null The contact data array or null if not found.
     */
    public function getContact(int $id): ?array
    {
        $contact = $this->storage->fetchOne($id);
        if (!$contact) {
            return null;
        }
        return [
            'id' => $contact['id'],
            'name' => $contact['name'],
            'mail' => $contact['mail'],
            'phone' => $contact['phone'],
            'address' => $contact['address'],
            'income' => (float) $contact['income'],
            'expense' => (float) $contact['expense'],
        ];
    }

    /**
     * Removes a contact from the storage.
     * Use this method to delete a contact.
     *
     * @param int $id The unique identifier of the contact to remove.
     */
    public function removeContact(int $id): void
    {
        $this->storage->remove($id);
    }

    /**
     * Saves a contact to the storage.
     * If an 'id' is present in the data, it updates the existing contact. Otherwise, it creates a new one.
     * Use this method to handle both creation and modification of contacts.
     *
     * @param array<string, int|string> $data The contact data including name, mail, phone, and address.
     */
    public function saveContact(array $data): void
    {
        if ($data['id'] ?? null) {
            $this->storage->update(
                (int) $data['id'],
                (string) ($data['name'] ?? ''),
                (string) ($data['mail'] ?? ''),
                (string) ($data['phone'] ?? ''),
                (string) ($data['address'] ?? ''),
            );
        } else {
            $this->storage->create(
                (string) ($data['name'] ?? ''),
                (string) ($data['mail'] ?? ''),
                (string) ($data['phone'] ?? ''),
                (string) ($data['address'] ?? ''),
            );
        }
    }
}
