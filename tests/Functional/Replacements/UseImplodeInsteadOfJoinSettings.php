<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Formatters\Replacements\UseImplodeInsteadOfJoin;
use Medas\PhpFormatter\Settings\Psr12;

readonly class UseImplodeInsteadOfJoinSettings extends Psr12
{
    public function __construct()
    {
        parent::__construct(
            additionalFormatters: [UseImplodeInsteadOfJoin::class],
        );
    }
}
