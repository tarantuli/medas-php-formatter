<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12VisibilityMarkers implements Formatter
{
    public function __construct(private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $this->sortVisibilityMarkers($tokens->structure);
    }

    private function sortVisibilityMarkers(Block $block): void
    {
        foreach ($block as $statement) {
            if (!$this->typeFinder->for($statement) instanceof FunctionDeclaration) {
                continue;
            }

            $abstractFinal = $statement->findToken([T_ABSTRACT, T_FINAL]);
            $visibility = $statement->findToken([T_PUBLIC, T_PROTECTED, T_PRIVATE]);
            $static = $statement->findToken(T_STATIC);

            if (!$visibility) {
                // Ensure that there is a visibility marker
                $visibility = clone $statement->firstToken();
                $visibility->id = T_PUBLIC;
                $visibility->text = 'public';
                $statement->prependToken($visibility);
            }

            if ($abstractFinal) {
                $statement->moveTokenAfter($abstractFinal, $visibility);
            }

            if ($static) {
                $statement->moveTokenAfter($visibility, $static);
            }
        }
    }
}
