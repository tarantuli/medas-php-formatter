<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ImportsFinder
{
    public function find(TokenCollection $tokens, ClassAnalysis $results)
    {
        foreach ($tokens as $token) {
            if ($token->is(T_USE) && $token->statement->type instanceof UseClassStatement) {
                $this->processImport($token, $results);
            }
        }
    }

    private function processImport(Token $token, ClassAnalysis $results): void
    {
        $path = $token->next->text;

        if ($token->next->next->is(T_AS)) {
            $reference = $token->next->next->next->text;
        }
        else {
            $reference = $this->getLastPart($path);
        }

        $results->imports[] = new ClassReference($reference, '\\' . $path);
    }

    private function getLastPart(string $path): string
    {
        return str_contains($path, '\\') ? substr($path, strrpos($path, '\\') + 1) : $path;
    }

}
