<?php

declare(strict_types=1);

namespace App;

use Medas\Core\Attributes\Service;

#[Service]
class SingleLineConstructor
{
    public function __construct(private Alpha $alpha, private Zeta $zeta)
    {
    }
}
