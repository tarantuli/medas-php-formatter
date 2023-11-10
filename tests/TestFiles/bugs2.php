<?php

declare(strict_types=1);

use Medas\Core\Attributes\ConfigValue;
use Medas\StorageManager\ConfigOptions\TypeDefaults\DefaultMaxIntegerValue;
use Medas\StorageManager\Interfaces\RecordSet;
use Medas\StorageManager\Structure\TypeHandlerFinder;

class ActionSet
{
    public RecordSet\Base|null $lastRecordSet = null;
    public mixed $lastInsertId = null;
}

foreach ($diff as $key => $value) {
    $fieldValue = $field->$key;
    $currentValue = $current->$key;
}

readonly class EntityStructureFinder
{
    public function __construct(
        private TypeHandlerFinder $typeHandlerFinder,

        #[ConfigValue(DefaultMaxIntegerValue::class)]
        private int               $defaultMaxIntegerValue,
    )
    {
    }
}
