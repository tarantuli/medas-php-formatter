<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\{BlankLinesBeforeBlocks,
    BlankLinesBetweenClassSections,
    Imports\NormalizeImports,
    Replacements\UseExitInsteadOfDie};
use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        $this->addPreparser(service(NoCommentsAtLineEnd::class));

        $this->addFormatter(service(NormalizeImports::class));
        $this->addFormatter(service(BlankLinesBetweenClassSections::class));
        $this->addFormatter(service(BlankLinesBeforeBlocks::class));
        $this->addFormatter(service(UseExitInsteadOfDie::class));
    }
}
