<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class PhpOpenTag implements StatementType
{

    public function __toString()
    {
        return 'php open tag';
    }
}
