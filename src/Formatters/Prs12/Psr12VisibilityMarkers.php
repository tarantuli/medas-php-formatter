<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Contexts\MethodDeclaration,
    Statement,
    StatementTypeFinder,
    StatementTypes\FunctionDeclaration,
    TokenTree
};

#[Service]
readonly class Psr12VisibilityMarkers extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 1500;
    }

    public function format(Job $job): void
    {
        $this->sortVisibilityMarkers($job->tree);
    }

    private function sortVisibilityMarkers(TokenTree $tree): void
    {
        foreach ($tree->statements() as $statement) {
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
