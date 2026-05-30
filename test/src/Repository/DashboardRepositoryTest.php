<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\DashboardRepository;
use App\Storage\InvoiceStorage;
use App\Storage\LedgerStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class DashboardRepositoryTest extends TestCase
{
    private $invoiceStorageMock;
    private $ledgerStorageMock;
    private DashboardRepository $repository;

    protected function setUp(): void
    {
        $this->invoiceStorageMock = $this->createMock(InvoiceStorage::class);
        $this->ledgerStorageMock = $this->createMock(LedgerStorage::class);
        $this->repository = new DashboardRepository($this->invoiceStorageMock, $this->ledgerStorageMock);
    }

    public function testAccounts(): void
    {
        $sums = [
            ['sum' => 1000.50],
            ['sum' => -200.25]
        ];
        $this->ledgerStorageMock->expects($this->once())->method('sumRealAccounts')->willReturn($this->yieldData($sums));

        $result = $this->repository->accounts();
        self::assertEquals(800.25, $result);
    }

    public function testCategories(): void
    {
        $sums = [
            ['name' => 'Büro', 'sum' => 50.0],
            ['name' => '', 'sum' => 20.0]
        ];
        $this->ledgerStorageMock->expects($this->once())->method('sumCategories')->willReturn($this->yieldData($sums));

        $result = $this->repository->categories();

        self::assertCount(2, $result);
        self::assertEquals('Büro', $result[0]['name']);
        self::assertEquals(50.0, $result[0]['amount']);
        self::assertEquals('ohne Zuordnung', $result[1]['name']);
        self::assertEquals(20.0, $result[1]['amount']);
    }

    public function testProblems(): void
    {
        $this->ledgerStorageMock->expects($this->once())->method('countUnassigned')->willReturn(3);
        $this->invoiceStorageMock->expects($this->once())->method('countUnassigned')->willReturn(5);
        $this->invoiceStorageMock->expects($this->once())->method('countWithoutDocument')->willReturn(2);

        $result = $this->repository->problems();

        self::assertCount(3, $result);
        self::assertEquals(3, $result[0]['count']);
        self::assertEquals(5, $result[1]['count']);
        self::assertEquals(2, $result[2]['count']);
    }

    private function yieldData(array $data): Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
