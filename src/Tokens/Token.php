<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

class Token extends \PhpToken
{
    public Block $block;
    public Statement $statement;
    public bool $inString;
    public bool $inAttribute;
    public Contexts\Context $context;

    public bool $spaceAfter = false;
    public bool $lineBreakAfter = false;

    public Token|null $previous = null;
    public Token|null $next = null;
}
