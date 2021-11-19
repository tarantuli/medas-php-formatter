<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12ElseifWhile implements BlockFormatter
{
    private ?Statement $previousStatement = null;

    public function __construct(private StatementTypeFinder $typeFinder)
    {
    }

    public function format(Block $block): void
    {
        foreach ($block as $statement) {
            if ($statement instanceof Block) {
                $this->format($statement);
                continue;
            }

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
