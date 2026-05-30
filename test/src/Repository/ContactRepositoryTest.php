<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\ContactRepository;
use App\Storage\ContactStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ContactRepositoryTest extends TestCase
{
    private $storageMock;
    private ContactRepository $repository;

    protected function setUp(): void
    {
        $this->storageMock = $this->createMock(ContactStorage::class);
        $this->repository = new ContactRepository($this->storageMock);
    }

    public function testListContactsAll(): void
    {
        $contacts = [['id' => 1, 'name' => 'Max']];
        $this->storageMock->expects($this->once())->method('fetchAll')->willReturn($this->yieldData($contacts));

        $result = $this->repository->listContacts('');
        self::assertEquals($contacts, $result);
    }

    public function testListContactsSearch(): void
    {
        $contacts = [['id' => 1, 'name' => 'Max']];
        $this->storageMock->expects($this->once())->method('fetchSome')->with('Max')->willReturn($this->yieldData($contacts));

        $result = $this->repository->listContacts('Max');
        self::assertEquals($contacts, $result);
    }

    public function testGetContact(): void
    {
        $contactData = [
            'id' => 1,
            'name' => 'Max',
            'mail' => 'max@example.com',
            'phone' => '123',
            'address' => 'Street',
            'income' => '100.50',
            'expense' => '50.25'
        ];
        $this->storageMock->expects($this->once())->method('fetchOne')->with(1)->willReturn($contactData);

        $result = $this->repository->getContact(1);

        self::assertNotNull($result);
        self::assertEquals(1, $result['id']);
        self::assertSame(100.5, $result['income']);
        self::assertSame(50.25, $result['expense']);
    }

    public function testGetContactNotFound(): void
    {
        $this->storageMock->expects($this->once())->method('fetchOne')->with(999)->willReturn(null);
        self::assertNull($this->repository->getContact(999));
    }

    public function testRemoveContact(): void
    {
        $this->storageMock->expects($this->once())->method('remove')->with(1);
        $this->repository->removeContact(1);
    }

    public function testSaveContactUpdate(): void
    {
        $data = ['id' => 1, 'name' => 'Neu'];
        $this->storageMock->expects($this->once())->method('update')->with(1, 'Neu', '', '', '');
        $this->repository->saveContact($data);
    }

    public function testSaveContactCreate(): void
    {
        $data = ['name' => 'Neu'];
        $this->storageMock->expects($this->once())->method('create')->with('Neu', '', '', '');
        $this->repository->saveContact($data);
    }

    private function yieldData(array $data): Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
