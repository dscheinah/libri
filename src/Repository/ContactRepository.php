<?php

namespace App\Repository;

use App\Storage\ContactStorage;

class ContactRepository
{
    public function __construct(
        private readonly ContactStorage $storage,
    ) {
    }

    /**
     * @return list<mixed>
     */
    public function listContacts(string $search): array
    {
        $contacts = $search ? $this->storage->fetchSome($search) : $this->storage->fetchAll();
        return iterator_to_array($contacts);
    }

    /**
     * @return array<string, mixed>|null
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

    public function removeContact(int $id): void
    {
        $this->storage->remove($id);
    }

    /**
     * @param array<string, int|string> $data
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
