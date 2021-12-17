<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

abstract class BaseFormatter implements Formatter
{
    public function additionalFormatters(): array
    {
        return [];
    }

    public function priority(): int
    {
        return 0;
    }
}
