<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Tokens\Statement;
use Medas\PhpFormatter\Tokens\StatementTypeFinder;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12ElseifWhile extends BaseFormatter
{
    private ?Statement $previousStatement = null;

    public function __construct(private readonly StatementTypeFinder $typeFinder)
    {
    }

    public function format(TokenTree $tree): void
    {
        foreach ($tree->statements() as $statement) {
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
