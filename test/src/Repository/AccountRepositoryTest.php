<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\AccountRepository;
use App\Storage\AccountStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class AccountRepositoryTest extends TestCase
{
    public function testListAccountsAll(): void
    {
        $storageMock = $this->createMock(AccountStorage::class);
        $storageMock->expects($this->once())->method('fetchAll')->willReturn($this->yieldAccounts());

        $repository = new AccountRepository($storageMock);
        $result = $repository->listAccounts(false);

        self::assertCount(2, $result);
        self::assertEquals('1000', $result[0]['no']);
        self::assertEquals('Kasse', $result[0]['name']);
        self::assertTrue($result[0]['real']);
        self::assertEquals('8000', $result[1]['no']);
        self::assertEquals('Erlöse', $result[1]['name']);
        self::assertFalse($result[1]['real']);
    }

    public function testListAccountsReal(): void
    {
        $storageMock = $this->createMock(AccountStorage::class);
        $storageMock->expects($this->once())->method('fetchReal')->willReturn($this->yieldRealAccounts());

        $repository = new AccountRepository($storageMock);
        $result = $repository->listAccounts(true);

        self::assertCount(1, $result);
        self::assertEquals('1000', $result[0]['no']);
        self::assertTrue($result[0]['real']);
    }

    public function testUpdateAccounts(): void
    {
        $storageMock = $this->createMock(AccountStorage::class);
        
        $nos = ['1000', '8000'];
        $names = ['Kasse Neu', 'Erlöse Neu'];
        $categories = ['', '5'];
        $reals = ['1000'];

        $matcher = $this->exactly(2);
        $storageMock->expects($matcher)->method('upsert')
            ->willReturnCallback(function (string $no, string $name, ?int $categoryId, bool $real) use ($matcher) {
                switch ($matcher->numberOfInvocations()) {
                    case 1:
                        self::assertEquals('1000', $no);
                        self::assertEquals('Kasse Neu', $name);
                        self::assertNull($categoryId);
                        self::assertTrue($real);
                        break;
                    case 2:
                        self::assertEquals('8000', $no);
                        self::assertEquals('Erlöse Neu', $name);
                        self::assertEquals(5, $categoryId);
                        self::assertFalse($real);
                        break;
                }
            });
        $storageMock->expects($this->once())->method('removeAllWithout')->with($nos);

        $repository = new AccountRepository($storageMock);
        $repository->updateAccounts($nos, $names, $categories, $reals);
    }

    private function yieldAccounts(): Generator
    {
        yield ['no' => '1000', 'name' => 'Kasse', 'category_id' => null, 'real' => 1];
        yield ['no' => '8000', 'name' => 'Erlöse', 'category_id' => 5, 'real' => 0];
    }

    private function yieldRealAccounts(): Generator
    {
        yield ['no' => '1000', 'name' => 'Kasse', 'category_id' => null, 'real' => 1];
    }
}
