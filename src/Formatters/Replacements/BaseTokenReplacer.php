<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Replacements;

use Medas\PhpBeautifier\Formatters\Formatter;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;

abstract class BaseTokenReplacer implements Formatter
{
    public function format(TokenCollection $tokens): void
    {
        foreach ($tokens as $token) {
            if ($this->doReplace($token)) {
                $this->update($token);
            }
        }
    }

    abstract protected function doReplace(Token $token): bool;

    abstract protected function update(Token $token): void;
}
