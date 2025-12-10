<?php

declare(strict_types=1);

clidie();

function getSortingWeightMethod(): Closure
{
    return function (array $file): int {
        return -strtotime($file['minFileCreate']);
    };
}
