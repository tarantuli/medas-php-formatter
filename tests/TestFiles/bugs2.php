<?php

declare(strict_types=1);

use Medas\StorageManager\Interfaces\RecordSet;

class ActionSet
{
    public RecordSet\Base|null $lastRecordSet = null;
    public mixed $lastInsertId = null;
}
