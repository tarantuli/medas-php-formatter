<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\Alignment\AlignArgumentNames;
use Medas\PhpFormatter\Formatters\BlankLines\{
    BlankLineBetweenObjectCreationAndManipulation,
    BlankLinesAfterPropertiesWithAttributes,
    BlankLinesBeforeBlocks,
    BlankLinesBetweenClassSections,
    BlankLinesBetweenStatementGroups,
    MedasElseifWhileCatch,
    NoLineBreakAfterOpenTagWithEcho
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
use Medas\PhpFormatter\Preformatters\NoCommentsAtLineEnd;
use Medas\PhpFormatter\Preparsers\ReorderClassElements;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        // Preformatters
        $this->preformatters[] = service(NoCommentsAtLineEnd::class);

        // Preparsers
        $this->preparsers[] = service(ReorderClassElements::class);

        // Formatters
        $this->formatters[] = service(AlignArgumentNames::class);
        $this->formatters[] = service(BlankLineBetweenObjectCreationAndManipulation::class);
        $this->formatters[] = service(BlankLinesAfterPropertiesWithAttributes::class);
        $this->formatters[] = service(BlankLinesBeforeBlocks::class);
        $this->formatters[] = service(BlankLinesBetweenClassSections::class);
        $this->formatters[] = service(BlankLinesBetweenStatementGroups::class);
        $this->formatters[] = service(KeepOriginalObjectOperatorBreaks::class);
        $this->formatters[] = service(LongLineSplitter::class);
        $this->formatters[] = service(MedasElseifWhileCatch::class);
        $this->formatters[] = service(NoLineBreakAfterOpenTagWithEcho::class);
        $this->formatters[] = service(NormalizeImports::class);
        $this->formatters[] = service(SoftLineSplitter::class);
        $this->formatters[] = service(TrailingCommaSplitter::class);
        $this->formatters[] = service(UseExitInsteadOfDie::class);
        $this->formatters[] = service(UseUnionNullInsteadOfNullable::class);

        // Breakpoint sets
        $this->classDeclarationSet = ClassDeclarationSet::instance();
        $this->controlStatementSet = ControlStatementSet::instance();
        $this->genericLineSet = GenericLineSet::instance();
        $this->softLineSet = SoftLineSet::instance();
    }
}
