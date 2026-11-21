<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{DeclarationKind, Statement, Token, TokenTree};

#[Service]
readonly class Psr12VisibilityMarkers extends BaseFormatter
{
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
            if ($statement->declaration?->kind === DeclarationKind::Method) {
                $this->processStatement($statement);
            }
        }
    }

    private function processStatement(Statement $statement): void
    {
        $abstractFinal = $this->findModifier($statement, [T_ABSTRACT, T_FINAL]);
        $visibility = $this->findModifier($statement, [T_PUBLIC, T_PROTECTED, T_PRIVATE]);
        $static = $this->findModifier($statement, T_STATIC);

        if (!$visibility) {
            // Ensure that there is a visibility marker, defaulting to "public"
            $visibility = $this->insertVisibility($statement);
        }

        if ($abstractFinal) {
            $statement->moveTokenAfter($visibility, $abstractFinal);
        }

        if ($static) {
            $statement->moveTokenAfter($static, $visibility);
        }
    }

    /**
     * Inserts "public" before the declaration itself, which comes after any doc comment, comments and attributes.
     */
    private function insertVisibility(Statement $statement): Token
    {
        $declarationStart = $statement->firstToken();

        while ($declarationStart->is([T_COMMENT, T_DOC_COMMENT, T_ATTRIBUTE]) || $declarationStart->inAttribute) {
            $declarationStart = $statement->getTokenAfter($declarationStart);
        }

        $visibility = Token::fromText('public', $declarationStart);

        $visibility->bracketDepth = $declarationStart->bracketDepth;
        $visibility->spaceAfter = true;

        if ($declarationStart === $statement->firstToken()) {
            $statement->prependToken($visibility);
        }
        else {
            $statement->insertTokenAfter($visibility, $declarationStart->previous);
        }

        return $visibility;
    }

    /**
     * Modifiers only appear before the "function" keyword; searching the whole statement would also
     * match a "static" return type or a "static::" in a parameter default.
     */
    private function findModifier(Statement $statement, array|int $type): Token|null
    {
        foreach ($statement as $token) {
            if ($token === $statement->declaration->keyword) {
                return null;
            }

            if ($token->is($type)) {
                return $token;
            }
        }

        return null;
    }
}
