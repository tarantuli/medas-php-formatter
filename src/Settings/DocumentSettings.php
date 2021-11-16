<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Settings\Indentations\Indentation;
use Medas\PhpBeautifier\Settings\Indentations\Space;
use Medas\PhpBeautifier\Settings\LineEndings\LineEnding;
use Medas\PhpBeautifier\Settings\LineEndings\LineFeed;

class DocumentSettings
{
    private LineEnding $lineEnding;
    private Indentation $indentation;

    public function __construct()
    {
        $this->lineEnding = new LineFeed();
        $this->indentation = new Space(4);
    }

    public function lineEnding(): LineEnding
    {
        return $this->lineEnding;
    }

    public function setLineEnding(LineEnding $lineEnding): self
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    public function indentation(): Indentation
    {
        return $this->indentation;
    }

    public function setIndentation(Indentation $indentation): self
    {
        $this->indentation = $indentation;

        return $this;
    }
}
