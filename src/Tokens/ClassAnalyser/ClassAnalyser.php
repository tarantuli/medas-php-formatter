<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\ClassAnalyser;

use Medas\PhpFormatter\Tokens\Tokenizer;
use Medas\PhpFormatter\Tokens\TokenTree;
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
        $tree = $this->tokenizer->makeTree($code);

        return $this->analyseTokenCollection($tree);
    }

    public function analyseTokenCollection(TokenTree $tree): ClassAnalysis
    {
        $results = new ClassAnalysis();

        $this->importsFinder->find($tree, $results);
        $this->nameFinder->find($tree, $results);
        $this->referenceFinder->find($tree, $results);

        return $results;
    }
}
