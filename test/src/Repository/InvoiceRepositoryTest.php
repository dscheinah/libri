<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\InvoiceRepository;
use App\Storage\AssignmentStorage;
use App\Storage\InvoiceStorage;
use App\Storage\MasterStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class InvoiceRepositoryTest extends TestCase
{
    private $invoiceStorageMock;
    private $assignmentStorageMock;
    private $masterStorageMock;
    private InvoiceRepository $repository;

    protected function setUp(): void
    {
        $this->invoiceStorageMock = $this->createMock(InvoiceStorage::class);
        $this->assignmentStorageMock = $this->createMock(AssignmentStorage::class);
        $this->masterStorageMock = $this->createMock(MasterStorage::class);
        
        $this->repository = new InvoiceRepository(
            $this->invoiceStorageMock,
            $this->assignmentStorageMock,
            $this->masterStorageMock
        );
    }

    public function testListInvoices(): void
    {
        $invoiceData = [
            [
                'id' => 1,
                'type' => 2,
                'date' => '2023-01-01',
                'description' => 'Test',
                'amount' => 100.0,
                'closed' => 1,
                'no_document' => 0,
                'finished' => 1,
                'reference' => 'REF-1'
            ]
        ];

        $this->invoiceStorageMock->expects($this->once())->method('fetchAll')->with(2)->willReturn($this->yieldData($invoiceData));

        $result = $this->repository->listInvoices(2, '');

        self::assertCount(1, $result);
        self::assertEquals(1, $result[0]['id']);
        self::assertEquals(100.0, $result[0]['amount']);
        self::assertTrue($result[0]['assigned']);
        self::assertEquals('REF-1', $result[0]['reference']);
    }

    public function testListOpenInvoices(): void
    {
        $invoiceData = [
            [
                'id' => 5,
                'type' => 1,
                'date' => '2023-02-01',
                'description' => 'Open',
                'amount' => 50.5,
                'reference' => 'REF-5'
            ]
        ];

        $this->invoiceStorageMock->expects($this->once())->method('fetchOpen')->willReturn($this->yieldData($invoiceData));

        $result = $this->repository->listOpenInvoices();

        self::assertCount(1, $result);
        self::assertEquals(5, $result[0]['id']);
        self::assertEquals(50.5, $result[0]['amount']);
    }

    public function testRemoveInvoice(): void
    {
        $this->invoiceStorageMock->expects($this->once())->method('removeOpenInvoice')->with(123)->willReturn(1);
        
        $result = $this->repository->removeInvoice(123);
        
        self::assertTrue($result);
    }

    private function yieldData(array $data): Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
