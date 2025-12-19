<?php

declare(strict_types=1);

namespace Test;

use Medas\Core\Attributes\Service;
use Shared\Api\RestEntity;

function test(string|null $cheese): Service|null
{
    $d = function (int|null $b) {
        return $b;
    };

    $e = fn(Service|null $service): Service|null => $service;

    return $a ? $b : $c;
}

class Tstsiodfsofijs
{
    public function getEntity(
        string     $path,
        int|null   $id = null,
        array|null $properties = null,
                   $queryParams = null
    ): RestEntity|null
    {
    }
}

function cast(array $values, string $className, ArrayToObjectCaster\Settings|null $settings = null): object
{
}

function fetchOrCreate(
    Selector\Selector $selector,
    array             $values,
    \Closure|null     $creationValues = null,
    bool              $persistOnCreate = true,
    bool              $flushOnPersist = true,
): object
{
}
