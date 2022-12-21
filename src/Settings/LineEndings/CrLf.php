<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings\LineEndings;

class CrLf implements LineEnding
{
    public function __toString(): string
    {
        return "\r\n";
    }
}
