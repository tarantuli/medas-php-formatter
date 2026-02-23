<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\{Preformatters\NoCommentsAtLineEnd, Settings\Psr12};

readonly class NoCommentsAtLineEndTestSettings extends Psr12
{
    public function __construct()
    {
        parent::__construct(
            additionalPreformatters: [NoCommentsAtLineEnd::class],
        );
    }
}
