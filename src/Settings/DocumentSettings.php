<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

class DocumentSettings
{
    private LineEndings\LineEnding $lineEnding;
    private Indentations\Indentation $indentation;
    private ?int $maxLineLength;

    public function lineEnding(): LineEndings\LineEnding
    {
        return $this->lineEnding;
    }

    public function setLineEnding(LineEndings\LineEnding $lineEnding): self
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    public function indentation(): Indentations\Indentation
    {
        return $this->indentation;
    }

    public function setIndentation(Indentations\Indentation $indentation): self
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
