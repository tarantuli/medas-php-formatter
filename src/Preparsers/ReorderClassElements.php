<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\Token;

#[Service]
readonly class ReorderClassElements implements Preparser
{
    public function __construct(
        private ReorderClassElements\ElementsFinder     $elementsFinder,
        private ReorderClassElements\ElementsAnalyzer   $elementsAnalyzer,
        private ReorderClassElements\ElementSorter      $elementSorter,
        private ReorderClassElements\SortedCodeCompiler $sortedCodeCompiler,
    )
    {
    }

    public function preparse(Job $job): void
    {
        $reorderingJob = new ReorderClassElements\ReorderingJob(Token::tokenize($job->code, TOKEN_PARSE));

        $this->elementsFinder->find($reorderingJob);
        $this->elementsAnalyzer->analyze($reorderingJob);
        $this->elementSorter->sort($reorderingJob);

        $job->code = $this->sortedCodeCompiler->compile($reorderingJob);
    }
}
