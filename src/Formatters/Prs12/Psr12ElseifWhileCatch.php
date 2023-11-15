<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class Psr12ElseifWhileCatch extends BaseFormatter
{
    public function priority(): int
    {
        return 1300;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            if ($statement->firstToken()->is([T_ELSE, T_ELSEIF, T_CATCH])) {
                $statement->mergeWithPrevious();
            }

            if ($statement->firstToken()->is(T_WHILE) && $statement->lastToken()->is(T_SEMICOLON)) {
                $statement->mergeWithPrevious();
            }
        }
    }
}
