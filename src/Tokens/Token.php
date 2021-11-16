<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

class Token extends \PhpToken
{
    public static function space(): self
    {
        return new self(T_WHITESPACE, ' ');
    }

    public Block $block;
    public Statement $statement;
    public bool $inString;
    public bool $inAttribute;
    public Contexts\Context $context;
}
