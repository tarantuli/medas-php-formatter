<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;

#[Service]
readonly class MedasElseifWhile extends BaseFormatter
{
    public function format(Job $job): void
    {
        foreach ($job->tree as $token) {
            if ($token->is([T_ELSE, T_ELSEIF])) {
                $token->previous->lineBreakAfter = true;
            }
        }
    }
}
