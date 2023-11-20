<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\{Alignment\AlignArgumentNames,
    BlankLines\BlankLinesAfterPropertiesWithAttributes,
    BlankLines\BlankLinesBeforeBlocks,
    BlankLines\BlankLinesBetweenClassSections,
    BlankLines\BlankLinesBetweenStatementGroups,
    BlankLines\MedasElseifWhileCatch,
    Imports\NormalizeImports,
    LineSplitting\KeepOriginalObjectOperatorBreaks,
    LineSplitting\LongLineSplitter,
    LineSplitting\TrailingCommaSplitter,
    Replacements\UseExitInsteadOfDie,
    Replacements\UseUnionNullInsteadOfNullable
};
use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;
use Medas\PhpTokenizer\AdditionalTokensDefiner;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        service(AdditionalTokensDefiner::class)->define();

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
        $this->addFormatter(service(TrailingCommaSplitter::class));
        $this->addFormatter(service(UseExitInsteadOfDie::class));
        $this->addFormatter(service(UseUnionNullInsteadOfNullable::class));
    }
}
