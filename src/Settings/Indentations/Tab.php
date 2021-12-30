<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings\Indentations;

class Tab implements Indentation
{
    public function __toString()
    {
        return "\t";
    }
}
