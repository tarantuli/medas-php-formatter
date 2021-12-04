<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\StatementTypes\ControlStatement;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $this->blankLineAdder->beforeTypes($tokens->structure, [
            ControlStatement::class,
        ]);
    }
}
