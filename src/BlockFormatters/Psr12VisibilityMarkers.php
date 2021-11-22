<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12VisibilityMarkers implements BlockFormatter
{
    public function __construct(private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(Block $block): void
    {
        $this->sortVisibilityMarkers($block);
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
                $visibility = clone $statement->firstToken();
                $visibility->id = T_PUBLIC;
                $visibility->text = 'public';
                $statement->prependToken($visibility);
            }

            if ($abstractFinal) {
                $statement->moveTokenAfter($visibility, $abstractFinal);
            }

            if ($static) {
                $statement->moveTokenAfter($static, $visibility);
            }
        }
    }
}
