<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Settings\LineEndings;

class CarriageReturn implements LineEnding
{

    public function __toString()
    {
        return "\r";
    }
}
