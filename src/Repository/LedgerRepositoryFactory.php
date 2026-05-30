<?php

namespace App\Repository;

use App\Storage\AccountStorage;
use App\Storage\AssignmentStorage;
use App\Storage\LedgerStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

/**
 * Factory for the LedgerRepository.
 */
class LedgerRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): LedgerRepository
    {
        $storage = $injector->get(LedgerStorage::class);
        assert($storage instanceof LedgerStorage);
        $assignmentStorage = $injector->get(AssignmentStorage::class);
        assert($assignmentStorage instanceof AssignmentStorage);
        $accountStorage = $injector->get(AccountStorage::class);
        assert($accountStorage instanceof AccountStorage);
        return new LedgerRepository(
            $storage,
            $assignmentStorage,
            $accountStorage,
        );
    }
}
