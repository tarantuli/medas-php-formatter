<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Settings\LineEndings;

class CrLf implements LineEnding
{

    public function __toString()
    {
        return "\r\n";
    }
}
