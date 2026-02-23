<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Formatters\Replacements\UseDieInsteadOfExit;
use Medas\PhpFormatter\Settings\Psr12;

readonly class UseDieInsteadOfExitSettings extends Psr12
{
    public function __construct()
    {
        parent::__construct(
            additionalFormatters: [UseDieInsteadOfExit::class],
        );
    }
}
