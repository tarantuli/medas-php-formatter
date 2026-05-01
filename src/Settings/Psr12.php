<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\BlankLines\NoBlankLinesAtStatementEnd;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointSet;
use Medas\PhpFormatter\Formatters\Psr12\{
    CurlyBlockDepthPropagator,
    KeywordsToLowercase,
    Psr12BlankLines,
    Psr12ElseifWhileCatch,
    Psr12VisibilityMarkers,
    Psr12Whitespace,
    SwitchIndentationAdder
};
use Medas\PhpFormatter\Formatters\Replacements\SingleLineControlBodiesEncloser;

readonly class Psr12 extends Settings
{
    public function __construct(
        array              $additionalFormatters = [],
        array              $additionalPreparsers = [],
        array              $additionalPreformatters = [],
        BreakpointSet|null $classDeclarationSet = null,
        BreakpointSet|null $controlStatementSet = null,
        BreakpointSet|null $genericLineSet = null,
        BreakpointSet|null $softLineSet = null,
    )
    {
        parent::__construct(
            [
                CurlyBlockDepthPropagator::class,
                KeywordsToLowercase::class,
                NoBlankLinesAtStatementEnd::class,
                Psr12BlankLines::class,
                Psr12ElseifWhileCatch::class,
                Psr12VisibilityMarkers::class,
                Psr12Whitespace::class,
                SingleLineControlBodiesEncloser::class,
                SwitchIndentationAdder::class,
                ...$additionalFormatters,
            ],
            $additionalPreparsers,
            $additionalPreformatters,
            $classDeclarationSet,
            $controlStatementSet,
            $genericLineSet,
            $softLineSet,
        );

        $this->document->setMaxLineLength(80);
    }
}
