<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

use Medas\Core\AsSingleton;

class PhpOpenTag implements StatementType
{
    use AsSingleton;

    public function __toString(): string
    {
        return 'php open tag';
    }
}
