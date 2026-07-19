<?php

namespace AppTest\Storage;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\LedgerStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class LedgerStorageTest extends TestCase
{
    private LedgerStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new LedgerStorage($this->backendMock);
    }

    public function testSumRealAccounts(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower($sql), 'sum(l.`amount`) as `sum` from `ledgers`')));
        $this->backendMock->expects($this->once())->method('fetch')
            ->willReturnCallback(function () { yield ['no' => '1000', 'name' => 'Test', 'sum' => 100.0]; });
        $result = iterator_to_array($this->storage->sumRealAccounts());
        self::assertCount(1, $result);
    }

    public function testSumCategories(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower($sql), 'sum(l.`amount`) as `sum` from `ledgers`')));
        $this->backendMock->expects($this->once())->method('fetch')
            ->willReturnCallback(function () { yield ['name' => 'Büro', 'sum' => 50.0]; });
        $result = iterator_to_array($this->storage->sumCategories());
        self::assertCount(1, $result);
    }

    public function testCountUnassigned(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () {
            yield ['count' => 5];
        });
        self::assertEquals(5, $this->storage->countUnassigned());
    }

    public function testFetchSome(): void
    {
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), ['%1000%', '%test%', '%test%', '%test%'])
            ->willReturnCallback(function () { yield ['id' => 1]; });
        $result = iterator_to_array($this->storage->fetchSome('1000', 'test'));
        self::assertCount(1, $result);
    }

    public function testFetchOpen(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () { yield ['id' => 1]; });
        $result = iterator_to_array($this->storage->fetchOpen());
        self::assertCount(1, $result);
    }

    public function testFetchOne(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () { yield ['id' => 1]; });
        self::assertNotNull($this->storage->fetchOne(1));
    }

    public function testUpdateCanceled(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static fn($sql) => str_contains(strtolower($sql), 'update `ledgers` set `canceled` = true')));
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), ['Reason', 1]);
        $this->storage->updateCanceled(1, 'Reason');
    }

    public function testCreate(): void
    {
        // Mock getNextInsertId
        $this->backendMock->expects($this->exactly(2))->method('prepare')
            ->willReturn('resource');
        $this->backendMock->expects($this->once())->method('fetch')
            ->willReturnCallback(function () {
                yield ['id' => 10];
            });
        
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [11, '2023-01-01', '1000', '8000', 100.0, 'Desc', 'Ref']);
        
        $id = $this->storage->create('2023-01-01', '1000', '8000', 100.0, 'Desc', 'Ref');
        self::assertEquals(11, $id);
    }

    public function testCreateTransfer(): void
    {
        $this->backendMock->expects($this->exactly(2))->method('prepare')
            ->willReturn('resource');
        $this->backendMock->expects($this->once())->method('fetch')
            ->willReturnCallback(function () { yield ['id' => 10]; });
        $this->backendMock->expects($this->once())->method('execute')
            ->with('resource', [11, '2023-01-01', '1000', '2000', 100.0, 'Transfer', 'Ref']);
        
        $id = $this->storage->createTransfer('2023-01-01', '1000', '2000', 100.0, 'Transfer', 'Ref');
        self::assertEquals(11, $id);
    }

    public function testFetchAmountBeforeDateByAccount(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () { yield ['amount' => 100.0]; });
        self::assertEquals(100.0, $this->storage->fetchAmountBeforeDateByAccount('1000', '2023-01-01'));
    }

    public function testFetchSummaryByAccount(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () { yield ['income' => 100.0, 'expense' => -50.0, 'total' => 50.0]; });
        $result = $this->storage->fetchSummaryByAccount('1000', '2023-01-01', '2023-01-31');
        self::assertEquals(100.0, $result['income']);
    }

    public function testFetchAmountBeforeDateByCategory(): void
    {
        $this->backendMock->method('fetch')->willReturnCallback(function () { yield ['amount' => 500.0]; });
        self::assertEquals(500.0, $this->storage->fetchAmountBeforeDateByCategory(1, '2023-01-01'));
    }
}
