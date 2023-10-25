<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports\Grouping;

use Medas\PhpFormatter\Formatters\Imports\ReferencesAndImports;

class Job
{
    /** @var Prefix[] */
    public array $prefixes = [];

    /** @var Prefix[] */
    public array $candidates = [];

    public array $statements = [];

    public function __construct(
        public readonly ReferencesAndImports $referencesAndImports,
    )
    {
    }
}
