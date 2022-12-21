<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings\Indentations;

class NoIndentation implements Indentation
{
    public function __toString(): string
    {
        return '';
    }
}
