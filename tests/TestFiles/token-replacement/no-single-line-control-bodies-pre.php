<?php

declare(strict_types=1);

if ($b === "" || $b === ".") {
    return $a;
}

if ($b === "" || $b === ".")
    return $a;

function __construct(int $type, array $extra)
{
    $this->type = $type;

    foreach ($extra as $key => $val)
        $this->{$key} = $val;
}
