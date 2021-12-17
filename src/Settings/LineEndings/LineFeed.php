<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings\LineEndings;

class LineFeed implements LineEnding
{
    public function __toString()
    {
        return "\n";
    }
}
