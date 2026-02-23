<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Formatters\Replacements\UseUnionNullInsteadOfNullable;
use Medas\PhpFormatter\Settings\Medas;

readonly class UseUnionNullInsteadOfNullableSettings extends Medas
{
    public function __construct()
    {
        parent::__construct(
            additionalFormatters: [UseUnionNullInsteadOfNullable::class],
        );
    }
}
