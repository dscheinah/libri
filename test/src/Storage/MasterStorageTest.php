<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\MasterStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class MasterStorageTest extends TestCase
{
    private MasterStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new MasterStorage($this->backendMock);
    }

    public function testFetchAllValues(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT `key`, `value` FROM `master` WHERE `value` IS NOT NULL');
        $this->backendMock->method('fetch')
            ->willReturnCallback(function () {
                yield ['key' => 'address', 'value' => 'Main St'];
            });

        $result = iterator_to_array($this->storage->fetchAllValues());
        self::assertCount(1, $result);
        self::assertEquals('address', $result[0]['key']);
    }

    public function testUpsert(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('INSERT INTO `master` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['address', 'New St', 'New St']);

        $this->storage->upsert('address', 'New St');
    }

    public function testUpsertWithEmptyValue(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['key', '', null]);

        $this->storage->upsert('key', '');
    }
}
