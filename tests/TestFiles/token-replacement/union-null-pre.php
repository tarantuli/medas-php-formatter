<?php

declare(strict_types=1);

use Medas\Core\Attributes\Service;

function test(?string $cheese): ?Service
{
    $d = function (?int $b) {
        return $b;
    };

    $e = fn(?Service $service): ?Service => $service;
    return $a ? $b : $c;
}
