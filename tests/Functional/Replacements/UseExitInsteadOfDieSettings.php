<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Formatters\Replacements\UseExitInsteadOfDie;
use Medas\PhpFormatter\Settings\Psr12;

readonly class UseExitInsteadOfDieSettings extends Psr12
{
    public function __construct()
    {
        parent::__construct(
            additionalFormatters: [UseExitInsteadOfDie::class],
        );
    }
}
