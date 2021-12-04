<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Formatters\Phases\FormattingPhase;
use Medas\PhpBeautifier\Tokens\TokenCollection;

interface Formatter
{
    public function format(TokenCollection $tokens): void;

    public function applyWhen(): FormattingPhase;

    public function additionalFormatters(): array;
}
