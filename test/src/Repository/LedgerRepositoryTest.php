<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\LedgerRepository;
use App\Storage\AccountStorage;
use App\Storage\AssignmentStorage;
use App\Storage\LedgerStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class LedgerRepositoryTest extends TestCase
{
    private $ledgerStorageMock;
    private $assignmentStorageMock;
    private $accountStorageMock;
    private LedgerRepository $repository;

    protected function setUp(): void
    {
        $this->ledgerStorageMock = $this->createMock(LedgerStorage::class);
        $this->assignmentStorageMock = $this->createMock(AssignmentStorage::class);
        $this->accountStorageMock = $this->createMock(AccountStorage::class);
        
        $this->repository = new LedgerRepository(
            $this->ledgerStorageMock,
            $this->assignmentStorageMock,
            $this->accountStorageMock
        );
    }

    public function testListLedgers(): void
    {
        $data = [[
            'id' => 1,
            'date' => '2023-01-01',
            'account_no' => '1000',
            'account_name' => 'Kasse',
            'offset_no' => '8000',
            'offset_name' => 'Erlöse',
            'description' => 'Test',
            'amount' => 100.5,
            'closed' => 0,
            'reference' => 'REF-1',
            'canceled' => 0,
            'transfer' => 0
        ]];
        $this->ledgerStorageMock->expects($this->once())->method('fetchSome')->with('1000', '')->willReturn($this->yieldData($data));

        $result = $this->repository->listLedgers('1000', '');

        self::assertCount(1, $result);
        self::assertEquals('1000 - Kasse', $result[0]['account']['description']);
        self::assertEquals(100.5, $result[0]['amount']);
    }

    public function testListOpenLedgers(): void
    {
        $data = [[
            'id' => 2,
            'date' => '2023-01-02',
            'description' => 'Open',
            'amount' => 50.0,
            'reference' => 'REF-2'
        ]];
        $this->ledgerStorageMock->expects($this->once())->method('fetchOpen')->willReturn($this->yieldData($data));

        $result = $this->repository->listOpenLedgers();
        self::assertCount(1, $result);
        self::assertEquals(2, $result[0]['id']);
    }

    public function testGetLedger(): void
    {
        $data = [
            'id' => 1,
            'date' => '2023-01-01',
            'account_no' => '1000',
            'account_name' => 'Kasse',
            'offset_no' => '8000',
            'offset_name' => 'Erlöse',
            'description' => 'Test',
            'amount' => 100.5,
            'closed' => 1,
            'reference' => 'REF-1',
            'canceled' => 0,
            'transfer' => 0
        ];
        $this->ledgerStorageMock->expects($this->once())->method('fetchOne')->with(1)->willReturn($data);
        $this->assignmentStorageMock->expects($this->once())->method('fetchAssignedInvoicesForLedger')->with(1)->willReturn($this->yieldData([]));

        $result = $this->repository->getLedger(1);
        self::assertNotNull($result);
        self::assertEquals(1, $result['id']);
        self::assertIsArray($result['invoices']);
    }

    public function testCancelLedger(): void
    {
        $this->ledgerStorageMock->expects($this->once())->method('updateCanceled')->with(1, 'Reason');
        $this->repository->cancelLedger(1, 'Reason');
    }

    private function yieldData(array $data): Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
