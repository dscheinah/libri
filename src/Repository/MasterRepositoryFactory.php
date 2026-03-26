<?php

namespace App\Repository;

use App\Storage\MasterStorage;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class MasterRepositoryFactory implements FactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function create(Injector $injector, array $options, string $class): MasterRepository
    {
        $storage = $injector->get(MasterStorage::class);
        assert($storage instanceof MasterStorage);
        return new MasterRepository(
            $storage,
        );
    }
}
