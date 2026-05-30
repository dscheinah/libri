<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\MasterRepository;
use App\Storage\MasterStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class MasterRepositoryTest extends TestCase
{
    private $storageMock;
    private MasterRepository $repository;

    protected function setUp(): void
    {
        $this->storageMock = $this->createMock(MasterStorage::class);
        $this->repository = new MasterRepository($this->storageMock);
    }

    public function testLoadEntries(): void
    {
        $data = [
            ['key' => 'address', 'value' => 'My Address'],
            ['key' => 'account', 'value' => 'DE123']
        ];
        $this->storageMock->expects($this->once())->method('fetchAllValues')->willReturn($this->yieldData($data));

        $result = $this->repository->loadEntries();

        self::assertEquals([
            'address' => 'My Address',
            'account' => 'DE123'
        ], $result);
    }

    public function testStoreEntries(): void
    {
        $data = [
            'address' => 'New Address',
            'ignore_me' => ['some' => 'array']
        ];

        $this->storageMock->expects($this->once())->method('upsert')->with('address', 'New Address');
        
        $this->repository->storeEntries($data);
    }

    private function yieldData(array $data): Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
