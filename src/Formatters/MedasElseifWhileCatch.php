<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;

#[Service]
readonly class MedasElseifWhileCatch extends BaseFormatter
{
    public function format(Job $job): void
    {
        foreach ($job->tree as $token) {
            if ($token->is([T_ELSE, T_ELSEIF, T_CATCH])) {
                $token->previous->lineBreakAfter = true;
            }
        }
    }
}
