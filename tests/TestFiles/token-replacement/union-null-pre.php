<?php

declare(strict_types=1);

use Medas\Core\Attributes\Service;
use Shared\Api\RestEntity;

function test(?string $cheese): ?Service
{
    $d = function (?int $b) {
        return $b;
    };

    $e = fn(?Service $service): ?Service => $service;
    return $a ? $b : $c;
}

class Tstsiodfsofijs
{
    public function getEntity(
        string $path,
        ?int   $id = null,
        ?array $properties = null,
               $queryParams = null
    ): ?RestEntity
    {
    }
}
