<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class NoBlankLinesAtStatementEnd extends BaseFormatter
{
    public function priority(): int
    {
        return 100;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            if ($statement === $statement->block->lastStatement()) {
                $statement->blankLineAfter(false);
                $statement->lastToken()->lineBreakAfter(false);
            }
        }
    }
}
