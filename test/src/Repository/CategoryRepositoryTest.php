<?php

namespace AppTest\Repository;

use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

use App\Repository\CategoryRepository;
use App\Storage\CategoryStorage;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class CategoryRepositoryTest extends TestCase
{
    public function testListCategories(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Bürobedarf'],
            ['id' => 2, 'name' => 'Reisekosten']
        ];

        $storageMock = $this->createMock(CategoryStorage::class);
        $storageMock->expects($this->once())->method('fetchAll')->willReturn($this->yieldCategories($categories));

        $repository = new CategoryRepository($storageMock);
        $result = $repository->listCategories();

        self::assertEquals($categories, $result);
    }

    public function testUpdateCategories(): void
    {
        $storageMock = $this->createMock(CategoryStorage::class);
        $names = ['Büro', 'Reise'];

        $matcher = $this->exactly(2);
        $storageMock->expects($matcher)->method('upsert')
            ->willReturnCallback(function (int $id, string $name) use ($matcher) {
                switch ($matcher->numberOfInvocations()) {
                    case 1:
                        self::assertEquals(1, $id);
                        self::assertEquals('Büro', $name);
                        break;
                    case 2:
                        self::assertEquals(2, $id);
                        self::assertEquals('Reise', $name);
                        break;
                }
            });
        $storageMock->expects($this->once())->method('removeAllAbove')->with(2);

        $repository = new CategoryRepository($storageMock);
        $repository->updateCategories($names);
    }

    private function yieldCategories(array $categories): Generator
    {
        foreach ($categories as $category) {
            yield $category;
        }
    }
}
