<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class AttributeStatement implements StatementType
{
    public function __toString()
    {
        return 'attribute statement';
    }
}
