<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\Token;

abstract readonly class BaseTokenReplacer extends BaseFormatter
{
    public function format(Job $job): void
    {
        foreach ($job->tree as $token) {
            if ($this->doReplace($token)) {
                $this->update($token);
            }
        }
    }

    abstract protected function doReplace(Token $token): bool;

    abstract protected function update(Token $token): void;
}
