<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\CategoryStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class CategoryStorageTest extends TestCase
{
    private CategoryStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new CategoryStorage($this->backendMock);
    }

    public function testFetchAll(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `categories` ORDER BY `id`');
        $this->backendMock->method('fetch')
            ->willReturnCallback(function () {
                yield ['id' => 1, 'name' => 'Büro'];
            });

        $result = iterator_to_array($this->storage->fetchAll());
        self::assertCount(1, $result);
        self::assertEquals('Büro', $result[0]['name']);
    }

    public function testUpsert(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('INSERT INTO `categories` (`id`, `name`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `name` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [1, 'Büro', 'Büro']);

        $this->storage->upsert(1, 'Büro');
    }

    public function testRemoveAllAbove(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('DELETE FROM `categories` WHERE `id` > ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [10]);

        $this->storage->removeAllAbove(10);
    }

    public function testFetchOne(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `categories` WHERE `id` = ?');
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1])
            ->willReturnCallback(function () {
                yield ['id' => 1, 'name' => 'Büro'];
            });

        $result = $this->storage->fetchOne(1);
        self::assertNotNull($result);
        self::assertEquals('Büro', $result['name']);
    }
}
