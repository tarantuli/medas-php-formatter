<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class AttributeStatement implements StatementType
{
    public function __toString()
    {
        return 'attribute statement';
    }
}
