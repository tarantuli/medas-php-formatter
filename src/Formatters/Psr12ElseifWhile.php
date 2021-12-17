<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12ElseifWhile extends BaseFormatter
{
    private ?Statement $previousStatement = null;

    public function __construct(private StatementTypeFinder $typeFinder)
    {
    }

    public function format(TokenTree $tree): void
    {
        foreach ($tree->block() as $statement) {
            if ($statement->firstToken()->is([T_ELSE, T_ELSEIF])) {
                $statement->mergeWithPrevious();
            }

            if ($statement->firstToken()->is(T_WHILE) && $statement->lastToken()->is(T_SEMICOLON)) {
                $statement->mergeWithPrevious();
            }

            $this->previousStatement = $statement;
        }
    }
}
