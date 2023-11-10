<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\FileBuilder\PhpClass\MethodDefinition;
use Medas\PdoStorage\Queries\Query;
use Medas\StorageManager\{
    Interfaces\Storage,
    Migrations\MigrationBuilder as MigrationBuilderInterface,
    StorageManager,
    Structure\Blueprint,
    UnitOfWork\Priority

};

#[Service]
readonly class MigrationBuilder implements MigrationBuilderInterface
{
    public function build(
        Storage          $storage,
        Blueprint        $expectedStructure,
        MethodDefinition $migrateMethod,
        MethodDefinition $undoMethod,
    ): bool
    {
        $queries = $this->buildActions($storage, $expectedStructure);

        if (count($queries) === 0) {
            return false;
        }

        $queryClass = Query::class;
        $storageManagerClass = StorageManager::class;
        $priorityClass = Priority::class;

        foreach ($queries as $query) {
            $queryString = addcslashes(trim($query->query), '"');
            $storageName = addcslashes(trim($query->storage()->name()), '"');
            $migrateMethod->body .= <<<PHP
\$unitOfWork->addAction(new \\$queryClass(
    <<<SQL
$queryString
SQL,
    [],
    service(\\$storageManagerClass::class)->byName("$storageName"),
    \\$priorityClass::{$query->priority()->name}
));
PHP;
        }

        return true;
    }
}
