<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\BlankLineAdder};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{StatementTypeFinder,
    StatementTypes\BlockCloser,
    StatementTypes\ClassDeclaration,
    StatementTypes\DeclareStatement,
    StatementTypes\FunctionDeclaration,
    StatementTypes\NamespaceDeclaration,
    StatementTypes\PhpOpenTag,
    StatementTypes\SwitchBranch,
    StatementTypes\UseClassStatement,
    StatementTypes\UseConstStatement,
    StatementTypes\UseFunctionStatement,
    TokenTree};

#[Service]
readonly class Psr12BlankLines extends BaseFormatter
{
    public function __construct(
        private BlankLineAdder $blankLineAdder,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(Job $job): void
    {
        $this->blankLineAdder->afterTypes($job->tree, [
            PhpOpenTag::class,
            DeclareStatement::class,
            NamespaceDeclaration::class,
            UseClassStatement::class,
            UseFunctionStatement::class,
            UseConstStatement::class,
        ]);
        $this->afterAttributes($job->tree);
        $this->afterMostComments($job->tree);
        $this->additionalLines($job->tree);
        $this->addSwitchIndentation($job->tree);
    }

    private function afterAttributes(TokenTree $tree): void
    {
        $bracketDepth = 0;

        foreach ($tree as $token) {
            if (!$token->inAttribute) {
                continue;
            }

            if ($token->is(T_SQUARE_BRACKET_OPEN)) {
                ++$bracketDepth;
            }

            if ($token->is(T_SQUARE_BRACKET_CLOSE)) {
                if ($bracketDepth === 0) {
                    $token->lineBreakAfter = true;
                }
                else {
                    --$bracketDepth;
                }
            }
        }
    }

    private function afterMostComments(TokenTree $tree): void
    {
        foreach ($tree as $token) {
            if ($token->is(T_COMMENT) && $token->next && $token->statement === $token->next->statement) {
                if ($token->statement->previous() && !$this->typeFinder->for($token->statement->previous()) instanceof SwitchBranch) {
                    $token->statement->previous()->blankLineAfter = true;
                }

                $token->lineBreakAfter = true;
                continue;
            }

            if (!$token->is(T_DOC_COMMENT)) {
                continue;
            }

            if ($this->isAtStartOfStatement($token)) {
                if ($token->statement->previous()) {
                    $token->statement->previous()->blankLineAfter = true;
                }

                $token->lineBreakAfter = true;
            }
        }
    }

    private function isAtStartOfStatement(mixed $token): bool
    {
        for ($index = 0; $index < $token->statement->getIndex($token); ++$index) {
            $earlierToken = $token->statement->getToken($index);

            if (!$earlierToken->inAttribute && !$earlierToken->is([T_COMMENT, T_ATTRIBUTE])) {
                return false;
            }
        }

        return true;
    }

    private function additionalLines(TokenTree $tree): void
    {
        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);

            if ($type instanceof ClassDeclaration) {
                $statement->getToken(-2)->lineBreakAfter = true;
            }

            if ($type instanceof FunctionDeclaration) {
                if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                    // It's a non-abstract function declaration
                    $statement->getToken(-2)->lineBreakAfter = true;
                }
                else {
                    // It's an abstract function declaration
                    $statement->blankLineAfter = true;
                }
            }

            if ($type instanceof BlockCloser && !$statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                $statement->blankLineAfter = true;
            }

            // No blank line needed before a case or default branch
            if ($type instanceof SwitchBranch && $previousStatement = $statement->previous()) {
                $previousStatement->blankLineAfter = false;
            }

            if ($statement === $statement->block->lastStatement()) {
                $statement->blankLineAfter = false;
                $statement->lastToken()->lineBreakAfter = false;
            }
        }
    }

    private function addSwitchIndentation(TokenTree $tree): void
    {
        $switchDepths = [];

        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);

            if ($statement->firstToken()->is(T_CURLY_BRACKET_CLOSE)) {
                $switchDepths = array_filter($switchDepths, fn($depth) => $depth !== $statement->block->depth + 1);
            }

            // Add additional depth levels to each statement equal to the number of open switch blocks
            // decreased by one if this statement itself is a case or default statement
            $statement->additionalDepth += count($switchDepths) - $type instanceof SwitchBranch;

            if ($statement->firstToken()->is(T_SWITCH)) {
                $switchDepths[] = $statement->block->depth + 1;
            }
        }
    }
}
