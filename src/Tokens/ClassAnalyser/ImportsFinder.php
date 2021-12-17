<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ImportsFinder
{
    public function __construct(
        private FqnProperties $fqnProperties,
    )
    {
    }

    public function find(TokenTree $tree, ClassAnalysis $results)
    {
        foreach ($tree as $token) {
            if ($token->is(T_USE) && $token->statement->type instanceof UseClassStatement) {
                $this->processImport($token->next, $results);
            }
        }
    }

    private function processImport(Token $token, ClassAnalysis $results): void
    {
        $path = $token->text;

        if ($token->next->is(T_AS)) {
            $reference = $token->next->next->text;
        }
        else {
            // The path does not start with a \, so it isn't a FQN, but that's no problem for getLastPart()
            $reference = $this->fqnProperties->getLastPart($path);
        }

        $results->imports[] = new ClassReference($reference, '\\' . $path);
    }
}
