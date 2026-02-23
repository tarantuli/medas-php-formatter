<?php

declare(strict_types=1);

namespace Medas\StorageManager\UnitOfWork;

use Medas\StorageManager\Interfaces\Storage;

class UnitOfWorkDocblockArrayType
{
    /** @var \SplObjectStorage<Storage> */
    private \SplObjectStorage $storages;
}
