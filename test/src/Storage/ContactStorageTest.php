<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\ContactStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class ContactStorageTest extends TestCase
{
    private ContactStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new ContactStorage($this->backendMock);
    }

    public function testFetchAll(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower((string)$sql), 'select `id`, `name`, `mail`, `phone` from `contacts`')));
        $this->backendMock->method('fetch')
            ->willReturnCallback(function () {
                yield ['id' => 1, 'name' => 'Max'];
            });

        $result = iterator_to_array($this->storage->fetchAll());
        self::assertCount(1, $result);
        self::assertEquals('Max', $result[0]['name']);
    }

    public function testFetchSome(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower((string)$sql), 'like ? or `mail` like ? or `phone` like ?')));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), ['%Max%', '%Max%', '%Max%'])
            ->willReturnCallback(function () {
                yield ['id' => 1, 'name' => 'Max'];
            });

        $result = iterator_to_array($this->storage->fetchSome('Max'));
        self::assertCount(1, $result);
    }

    public function testFetchOne(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static function ($sql) {
                return str_contains(str_replace(["\n", "\r", ' '], '', strtolower((string)$sql)), 'selectc.*,sum(if(i.`amount`>0');
            }));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1])
            ->willReturnCallback(function () {
                yield ['id' => 1, 'name' => 'Max', 'income' => 100.0, 'expense' => -50.0];
            });

        $result = $this->storage->fetchOne(1);
        self::assertNotNull($result);
        self::assertEquals(100.0, $result['income']);
    }

    public function testRemove(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('DELETE FROM `contacts` WHERE `id` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [1]);

        $this->storage->remove(1);
    }

    public function testUpdate(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower((string)$sql), 'update `contacts` set `name` = ?, `mail` = ?, `phone` = ?, `address` = ? where `id` = ?')));
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Max', 'max@example.com', '123', 'Street', 1]);

        $this->storage->update(1, 'Max', 'max@example.com', '123', 'Street');
    }

    public function testUpdateWithNulls(): void
    {
        $this->backendMock->expects($this->once())->method('prepare');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Max', null, null, null, 1]);

        $this->storage->update(1, 'Max', '', '', '');
    }

    public function testCreate(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower((string)$sql), 'insert into `contacts` (`name`, `mail`, `phone`, `address`) values (?, ?, ?, ?)')));
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Max', 'max@example.com', '123', 'Street']);

        $this->storage->create('Max', 'max@example.com', '123', 'Street');
    }

    public function testCreateWithNulls(): void
    {
        $this->backendMock->expects($this->once())->method('prepare');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Max', null, null, null]);

        $this->storage->create('Max', '', '', '');
    }
}
