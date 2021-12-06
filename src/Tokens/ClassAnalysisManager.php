<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Tokens\ClassAnalyser\ClassAnalyser;
use Medas\PhpBeautifier\Tokens\ClassAnalyser\ClassAnalysis;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ClassAnalysisManager
{
    public function __construct(private ClassAnalyser $analyser)
    {
    }

    public function forTokens(TokenCollection $tokens): ClassAnalysis
    {
        return $this->analyser->analyseTokenCollection($tokens);
    }
}
