<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\Alignment\AlignArgumentNames;
use Medas\PhpFormatter\Formatters\BlankLines\{
    BlankLinesAfterPropertiesWithAttributes,
    BlankLinesBeforeBlocks,
    BlankLinesBetweenClassSections,
    BlankLinesBetweenStatementGroups,
    MedasElseifWhileCatch
};
use Medas\PhpFormatter\Formatters\Imports\NormalizeImports;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Sets\{
    ClassDeclarationSet,
    ControlStatementSet,
    GenericLineSet,
    SoftLineSet
};
use Medas\PhpFormatter\Formatters\LineSplitting\KeepOriginalObjectOperatorBreaks;
use Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter;
use Medas\PhpFormatter\Formatters\LineSplitting\SoftLineSplitter;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpFormatter\Formatters\Replacements\{UseExitInsteadOfDie, UseUnionNullInsteadOfNullable};
use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        $this->addPreparser(service(NoCommentsAtLineEnd::class));
        $this->addFormatter(service(AlignArgumentNames::class));
        $this->addFormatter(service(BlankLinesAfterPropertiesWithAttributes::class));
        $this->addFormatter(service(BlankLinesBeforeBlocks::class));
        $this->addFormatter(service(BlankLinesBetweenClassSections::class));
        $this->addFormatter(service(BlankLinesBetweenStatementGroups::class));
        $this->addFormatter(service(KeepOriginalObjectOperatorBreaks::class));
        $this->addFormatter(service(LongLineSplitter::class));
        $this->addFormatter(service(MedasElseifWhileCatch::class));
        $this->addFormatter(service(NormalizeImports::class));
        $this->addFormatter(service(SoftLineSplitter::class));
        $this->addFormatter(service(TrailingCommaSplitter::class));
        $this->addFormatter(service(UseExitInsteadOfDie::class));
        $this->addFormatter(service(UseUnionNullInsteadOfNullable::class));

        // Breakpoint sets
        $this->classDeclarationSet = ClassDeclarationSet::instance();
        $this->controlStatementSet = ControlStatementSet::instance();
        $this->genericLineSet = GenericLineSet::instance();
        $this->softLineSet = SoftLineSet::instance();
    }
}
