<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\PhpFormatter\Tokens\ClassAnalyser\ClassAnalyser;
use Medas\PhpFormatter\Tokens\ClassAnalyser\ClassAnalysis;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ClassAnalysisManager
{
    public function __construct(
        private ClassAnalyser $analyser,
    )
    {
    }

    public function forTokens(TokenTree $tree): ClassAnalysis
    {
        return $this->analyser->analyseTokenCollection($tree);
    }
}
