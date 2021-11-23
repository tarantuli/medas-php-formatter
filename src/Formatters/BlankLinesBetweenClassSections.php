<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\StatementTypes\ClassConstDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\Comment;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBetweenClassSections implements Formatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $this->blankLineAdder->afterTypes($tokens->structure, [
            ClassConstDeclaration::class,
            ClassPropertyDeclaration::class,
        ]);

        $this->blankLineAdder->beforeTypes($tokens->structure, [
            Comment::class,
        ]);
    }
}
