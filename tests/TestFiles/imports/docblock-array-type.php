<?php

declare(strict_types=1);

namespace Medas\StorageManager\UnitOfWork;

use Medas\PhpTokenizer\{Statement, Token};
use Medas\StorageManager\Interfaces\Storage;

class UnitOfWorkDocblockArrayType
{
    /** @var \SplObjectStorage<Storage> */
    private \SplObjectStorage $storages;

    /** @return array{Token[][], int|null} */
    public function extract(Statement $statement): array
    {
    }
}
