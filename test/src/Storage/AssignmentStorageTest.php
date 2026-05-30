<?php

namespace AppTest\Storage;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Storage\AssignmentStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sx\Data\BackendInterface;

#[AllowMockObjectsWithoutExpectations]
class AssignmentStorageTest extends TestCase
{
    private AssignmentStorage $storage;
    private MockObject $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->storage = new AssignmentStorage($this->backendMock);
    }

    public function testFetchOpenLedger(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('SELECT `id`, `amount` FROM `ledgers`'));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1])
            ->willReturnCallback(function () {
                yield ['id' => 1, 'amount' => 100.0];
            });

        $result = $this->storage->fetchOpenLedger(1);
        self::assertNotNull($result);
        self::assertEquals(100.0, $result['amount']);
    }

    public function testFetchOpenInvoice(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('SELECT `id`, `amount` FROM `invoices`'));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [2])
            ->willReturnCallback(function () {
                yield ['id' => 2, 'amount' => 50.0];
            });

        $result = $this->storage->fetchOpenInvoice(2);
        self::assertNotNull($result);
        self::assertEquals(50.0, $result['amount']);
    }

    public function testCreateAssignment(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::stringContains('INSERT INTO `ledgers_x_invoices`'));
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [1, 2]);

        $this->storage->createAssignment(1, 2);
    }

    public function testMarkLedgerClosed(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('UPDATE `ledgers` SET `closed` = 1 WHERE `id` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [1]);

        $this->storage->markLedgerClosed(1);
    }

    public function testMarkInvoiceClosed(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with('UPDATE `invoices` SET `closed` = 1 WHERE `id` = ?');
        $this->backendMock->expects($this->once())->method('execute')
            ->with(self::anything(), [2]);

        $this->storage->markInvoiceClosed(2);
    }

    public function testFetchAssignedInvoicesForLedger(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static function ($sql) {
                return str_contains(str_replace(["\n", "\r", ' '], '', strtolower((string)$sql)), 'from`invoices`iinnerjoin`ledgers_x_invoices`');
            }));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [1])
            ->willReturnCallback(function () {
                yield ['id' => 10, 'description' => 'Inv 10'];
            });

        $result = iterator_to_array($this->storage->fetchAssignedInvoicesForLedger(1));
        self::assertCount(1, $result);
    }

    public function testFetchAssignedLedgersForInvoice(): void
    {
        $this->backendMock->expects($this->once())->method('prepare')
            ->with(self::callback(static function ($sql) {
                return str_contains(str_replace(["\n", "\r", ' '], '', strtolower((string)$sql)), 'from`ledgers`linnerjoin`ledgers_x_invoices`');
            }));
        $this->backendMock->expects($this->once())->method('fetch')
            ->with(self::anything(), [2])
            ->willReturnCallback(function () {
                yield ['id' => 20, 'description' => 'Led 20'];
            });

        $result = iterator_to_array($this->storage->fetchAssignedLedgersForInvoice(2));
        self::assertCount(1, $result);
    }
}
