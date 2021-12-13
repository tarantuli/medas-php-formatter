<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\Tokenizer;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ClassAnalyser
{
    public function __construct(
        private ImportsFinder   $importsFinder,
        private NameFinder      $nameFinder,
        private ReferenceFinder $referenceFinder,
        private Tokenizer       $tokenizer,
    )
    {
    }

    public function analyse(string $code): ClassAnalysis
    {
        $tokens = $this->tokenizer->tokenize($code);

        return $this->analyseTokenCollection($tokens);
    }

    public function analyseTokenCollection(TokenCollection $tokens): ClassAnalysis
    {
        $results = new ClassAnalysis();

        $this->importsFinder->find($tokens, $results);
        $this->nameFinder->find($tokens, $results);
        $this->referenceFinder->find($tokens, $results);

        return $results;
    }
}
