<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class Psr12ElseifWhile extends BaseFormatter
{
    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            if ($statement->firstToken()->is([T_ELSE, T_ELSEIF])) {
                $statement->mergeWithPrevious();
            }

            if ($statement->firstToken()->is(T_WHILE) && $statement->lastToken()->is(T_SEMICOLON)) {
                $statement->mergeWithPrevious();
            }
        }
    }
}
