<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\PhpFormatter\Tokens\ClassAnalyser\{ClassAnalyser, ClassAnalysis};
use Medas\ServiceManager\Service;

#[Service]
class ClassAnalysisManager
{
    public function __construct(
        private readonly ClassAnalyser $analyser,
    )
    {
    }

    public function forTokens(TokenTree $tree): ClassAnalysis
    {
        return $this->analyser->analyseTokenCollection($tree);
    }
}
