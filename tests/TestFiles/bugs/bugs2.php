<?php

declare(strict_types=1);

use Medas\Core\Attributes\ConfigValue;
use Medas\FileBuilder\PhpClass\MethodDefinition;
use Medas\StorageManager\ConfigOptions\TypeDefaults\DefaultMaxIntegerValue;
use Medas\StorageManager\Interfaces\{RecordSet, Storage};
use Medas\StorageManager\Structure\{Blueprint, TypeHandlerFinder};

class ActionSetx
{
    public RecordSet\Base|null $lastRecordSet = null;
    public mixed $lastInsertId = null;
}

foreach ($diff as $key => $value) {
    $fieldValue = $field->$key;
    $currentValue = $current->$key;
}

readonly class EntityStructureFinderx
{
    public function __construct(
        private TypeHandlerFinder $typeHandlerFinder,

        #[ConfigValue(DefaultMaxIntegerValue::class)]
        private int               $defaultMaxIntegerValue,
    )
    {
    }
}

interface MigrationBuilderx
{
    public function build(
        Storage          $storage,
        Blueprint        $expectedStructure,
        MethodDefinition $migrateMethod,
        MethodDefinition $undoMethod,
    ): bool;

    public function buildActions(Storage $storage, Blueprint $blueprint): ActionSet;
}
