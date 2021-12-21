<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Formatters\BlankLinesBeforeBlocks;
use Medas\PhpBeautifier\Formatters\BlankLinesBetweenClassSections;
use Medas\PhpBeautifier\Formatters\Imports\NormalizeImports;
use Medas\PhpBeautifier\Formatters\Replacements\UseExitInsteadOfDie;
use Medas\PhpBeautifier\Preparsers\NoCommentsAtLineEnd;

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
