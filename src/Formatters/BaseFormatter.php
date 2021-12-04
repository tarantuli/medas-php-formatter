<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Formatters\Phases\AfterDeterminingContext;
use Medas\PhpBeautifier\Formatters\Phases\FormattingPhase;

abstract class BaseFormatter implements Formatter
{
    public function applyWhen(): FormattingPhase
    {
        return new AfterDeterminingContext();
    }

    public function additionalFormatters(): array
    {
        return [];
    }
}
