<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\InvoiceStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class InvoiceStorageTest extends TestCase
{
    private InvoiceStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new InvoiceStorage($this->backendMock);
    }

    public function testCountUnassigned(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () {
            yield ['count' => 5];
        });
        self::assertEquals(5, $this->storage->countUnassigned());
    }

    public function testCountWithoutDocument(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () {
            yield ['count' => 3];
        });
        self::assertEquals(3, $this->storage->countWithoutDocument());
    }

    public function testFetchAll(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static function ($sql) {
                return str_contains(str_replace(["\n", "\r", ' ', '`'], '', strtolower((string)$sql)), 'whereidtype=?') === false;
            }));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1])
            ->willReturnCallback(function () {
                yield ['id' => 10, 'amount' => 100.0];
            });

        $result = iterator_to_array($this->storage->fetchAll(1));
        self::assertCount(1, $result);
    }

    public function testFetchSome(): void
    {
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1, '%test%', '%test%', '%test%'])
            ->willReturnCallback(function () {
                yield ['id' => 10];
            });
        $result = iterator_to_array($this->storage->fetchSome(1, 'test'));
        self::assertCount(1, $result);
    }

    public function testFetchOpen(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('WHERE `closed` = false AND (`type` = 1 OR `finished` = true)'));
        $this->backendMock->method('fetch')->willReturnCallback(function () {
            yield ['id' => 1];
        });
        $result = iterator_to_array($this->storage->fetchOpen());
        self::assertCount(1, $result);
    }

    public function testFetchOne(): void
    {
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [10])
            ->willReturnCallback(function () {
                yield ['id' => 10];
            });
        self::assertNotNull($this->storage->fetchOne(10));
    }

    public function testFetchOpenInvoice(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('WHERE `id` = ? AND `closed` = false AND `finished` = false'));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [10])
            ->willReturnCallback(function () {
                yield ['id' => 10];
            });
        self::assertNotNull($this->storage->fetchOpenInvoice(10));
    }

    public function testRemoveOpenInvoice(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [10])
            ->willReturn(1);
        self::assertEquals(1, $this->storage->removeOpenInvoice(10));
    }

    public function testCreate(): void
    {
        $this->backendMock->expects($this->once())->method('insert')
            ->with(self::anything(), [1, '2023-01-01', 100.0, 'Desc', 'Ref', true, 'Addr', 5])
            ->willReturn(123);
        
        $id = $this->storage->create(1, '2023-01-01', 100.0, 'Desc', 'Ref', true, 'Addr', 5);
        self::assertEquals(123, $id);
    }

    public function testUpdateAll(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['2023-01-01', 100.0, 'Desc', 'Ref', true, 'Addr', 5, 10]);
        
        $this->storage->updateAll(10, '2023-01-01', 100.0, 'Desc', 'Ref', true, 'Addr', 5);
    }

    public function testUpdateDetails(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Desc', 'Ref', true, 5, 10]);
        
        $this->storage->updateDetails(10, 'Desc', 'Ref', true, 5);
    }

    public function testUpdateReference(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['NewRef', 10]);
        $this->storage->updateReference(10, 'NewRef');
    }

    public function testUpdateDocument(): void
    {
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['file.pdf', 'content', 10]);
        $this->storage->updateDocument(10, 'file.pdf', 'content');
    }

    public function testFetchForReport(): void
    {
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), ['2023-01-01', '2023-01-31'])
            ->willReturnCallback(function () { yield ['id' => 1]; });
        $result = iterator_to_array($this->storage->fetchForReport('2023-01-01', '2023-01-31'));
        self::assertCount(1, $result);
    }

    public function testMarkFinished(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('UPDATE `invoices` SET `finished` = 1 WHERE `id` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [10]);
        $this->storage->markFinished(10);
    }
}
