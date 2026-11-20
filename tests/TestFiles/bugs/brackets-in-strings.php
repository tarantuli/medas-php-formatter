<?php

declare(strict_types=1);

function describe(string $encoding, array $match): string
{
    if ($encoding === '') {
        throw new RuntimeException("Unsupported encoding [$encoding]");
    }

    return "$encoding (since {$match[1]})";
}
