<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\AccountStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class AccountStorageTest extends TestCase
{
    private AccountStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new AccountStorage($this->backendMock);
    }

    public function testFetchAll(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `accounts` ORDER BY `no`');
        $this->backendMock->method('fetch')
            ->willReturnCallback(function () {
                yield ['no' => '1000', 'name' => 'Kasse'];
            });

        $result = iterator_to_array($this->storage->fetchAll());
        self::assertCount(1, $result);
        self::assertEquals('1000', $result[0]['no']);
    }

    public function testFetchReal(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `accounts` WHERE `real` = true ORDER BY `no`');
        $this->backendMock->method('fetch')
            ->willReturnCallback(function () {
                yield ['no' => '1000', 'real' => true];
            });

        $result = iterator_to_array($this->storage->fetchReal());
        self::assertCount(1, $result);
    }

    public function testFetchOne(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `accounts` WHERE `no` = ?');
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), ['1000'])
            ->willReturnCallback(function () {
                yield ['no' => '1000'];
            });

        $result = $this->storage->fetchOne('1000');
        self::assertNotNull($result);
        self::assertEquals('1000', $result['no']);
    }

    public function testFetchOneNotFound(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () {
            yield;
        });

        $result = $this->storage->fetchOne('999');
        self::assertNull($result);
    }

    public function testFetchOneReal(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('SELECT * FROM `accounts` WHERE `real` = true AND `no` = ?');
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), ['1000'])
            ->willReturnCallback(function () {
                yield ['no' => '1000', 'real' => true];
            });

        $result = $this->storage->fetchOneReal('1000');
        self::assertNotNull($result);
    }

    public function testUpsert(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('INSERT INTO `accounts`'));
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['1000', 'Bank', 1, true, 'Bank', 1, true]);

        $this->storage->upsert('1000', 'Bank', 1, true);
    }

    public function testRemoveAllWithout(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('DELETE FROM `accounts` WHERE `no` NOT IN (?,?)');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['1000', '1100']);

        $this->storage->removeAllWithout(['1000', '1100']);
    }
}
