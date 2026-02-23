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
use Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;
use Medas\PhpFormatter\Preformatters\NoCommentsAtLineEnd;
use Medas\PhpFormatter\Preparsers\ReorderClassElements;

readonly class Medas extends Psr12
{
    public function __construct(
        array $additionalFormatters = [],
    )
    {
        parent::__construct(
            [
                AlignArgumentNames::class,
                BlankLineBetweenObjectCreationAndManipulation::class,
                BlankLinesAfterPropertiesWithAttributes::class,
                BlankLinesBeforeBlocks::class,
                BlankLinesBetweenClassSections::class,
                BlankLinesBetweenStatementGroups::class,
                KeepOriginalObjectOperatorBreaks::class,
                LongLineSplitter::class,
                MedasElseifWhileCatch::class,
                NoLineBreakAfterOpenTagWithEcho::class,
                NormalizeImports::class,
                ServiceConstructionParameters::class,
                SoftLineSplitter::class,
                TrailingCommaSplitter::class,
                UseExitInsteadOfDie::class,
                UseUnionNullInsteadOfNullable::class,
                ...$additionalFormatters,
            ],
            [ReorderClassElements::class],
            [NoCommentsAtLineEnd::class],
            ClassDeclarationSet::instance(),
            ControlStatementSet::instance(),
            GenericLineSet::instance(),
            SoftLineSet::instance(),
        );
    }
}
