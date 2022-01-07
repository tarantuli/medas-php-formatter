<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

use Medas\ServiceManager\AsSingleton;

class AttributeStatement implements StatementType
{
    use AsSingleton;

    public function __toString()
    {
        return 'attribute statement';
    }
}
