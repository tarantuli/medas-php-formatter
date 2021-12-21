<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Settings\Indentations\Indentation;
use Medas\PhpBeautifier\Settings\LineEndings\LineEnding;

class DocumentSettings
{
    private LineEnding $lineEnding;
    private Indentation $indentation;
    private ?int $maxLineLength;

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

    public function maxLineLength(): int
    {
        return $this->maxLineLength;
    }

    public function setMaxLineLength(int $maxLineLength): self
    {
        $this->maxLineLength = $maxLineLength;

        return $this;
    }
}
