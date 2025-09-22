<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class NoLineBreakAfterOpenTagWithEcho extends BaseFormatter
{
    public function priority(): int
    {
        return 101;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            if ($statement->containsType(T_OPEN_TAG_WITH_ECHO)) {
                $statement->next()->mergeWithPrevious();
            }
        }
    }
}
