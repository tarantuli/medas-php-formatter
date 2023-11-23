<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class MedasElseifWhileCatch extends BaseFormatter
{
    public function priority(): int
    {
        return 1100;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree as $token) {
            if ($token->is([T_ELSE, T_ELSEIF, T_CATCH])) {
                $token->previous->lineBreakAfter();
            }
        }
    }
}
