<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\Contexts\MethodDeclaration;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12VisibilityMarkers extends BaseFormatter
{
    public function __construct(private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(TokenTree $tree): void
    {
        $this->sortVisibilityMarkers($tree);
    }

    private function sortVisibilityMarkers(TokenTree $tree): void
    {
        foreach ($tree->block() as $statement) {
            if (!$this->typeFinder->for($statement) instanceof FunctionDeclaration) {
                continue;
            }

            if (!$statement->firstToken()->context instanceof MethodDeclaration) {
                continue;
            }

            $this->processStatement($statement);
        }
    }

    private function processStatement(Statement $statement): void
    {
        $abstractFinal = $statement->findToken([T_ABSTRACT, T_FINAL]);
        $visibility = $statement->findToken([T_PUBLIC, T_PROTECTED, T_PRIVATE]);
        $static = $statement->findToken(T_STATIC);

        if (!$visibility) {
            // Ensure that there is a visibility marker, defaulting to "public"
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
