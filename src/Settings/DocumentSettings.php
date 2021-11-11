<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Settings;

use Medas\PhpReformatter\Settings\LineEndings\LineEnding;
use Medas\PhpReformatter\Settings\LineEndings\LineFeed;

class DocumentSettings
{
    private LineEnding $lineEnding;

    public function __construct()
    {
        $this->lineEnding = new LineFeed();
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
}
