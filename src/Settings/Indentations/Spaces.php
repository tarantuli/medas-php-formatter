<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings\Indentations;

class Spaces implements Indentation
{
    private string $indentation;

    public function __construct(private int $length)
    {
        $this->indentation = str_repeat(' ', $this->length);
    }

    public function __toString()
    {
        return $this->indentation;
    }
}
