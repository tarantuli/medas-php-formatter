<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpTokenizer\{Token, TokenTree};

abstract class BaseTokenReplacer extends BaseFormatter
{
    public function format(TokenTree $tree): void
    {
        foreach ($tree as $token) {
            if ($this->doReplace($token)) {
                $this->update($token);
            }
        }
    }

    abstract protected function doReplace(Token $token): bool;

    abstract protected function update(Token $token): void;
}
