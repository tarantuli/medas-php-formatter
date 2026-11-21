<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\BlankLineAdder};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    BlockType,
    DeclarationKind,
    StatementTypeFinder,
    StatementTypes\BlockCloser,
    StatementTypes\DeclareStatement,
    StatementTypes\NamespaceDeclaration,
    StatementTypes\PhpOpenTag,
    StatementTypes\SwitchBranch,
    StatementTypes\UseClassStatement,
    StatementTypes\UseConstStatement,
    StatementTypes\UseFunctionStatement,
    TokenGroups,
    TokenTree,
    TokenType
};

#[Service]
readonly class Psr12BlankLines extends BaseFormatter
{
    public function __construct(
        private BlankLineAdder      $blankLineAdder,
        private StatementTypeFinder $typeFinder,
        private TokenGroups         $tokenGroups,
    )
    {
    }

    public function priority(): int
    {
        return 1300;
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
    }

    /**
     * A line break after the attributes of a declaration, but not after those of a parameter, which are inside the
     * parentheses of the parameter list.
     */
    private function afterAttributes(TokenTree $tree): void
    {
        foreach ($tree as $token) {
            if ($token->closesAttribute && $token->bracketDepth === 0) {
                $token->lineBreakAfter();
            }
        }
    }

    private function afterMostComments(TokenTree $tree): void
    {
        foreach ($tree as $token) {
            if ($token->is(T_COMMENT) && $token->next && $token->statement === $token->next->statement) {
                if ($token->statement->previous()
                        && !$this->typeFinder->for($token->statement->previous()) instanceof SwitchBranch) {
                    $token->statement->previous()->blankLineAfter();
                }

                $token->lineBreakAfter();

                continue;
            }

            if (!$token->is(T_DOC_COMMENT)) {
                continue;
            }

            if ($this->isAtStartOfStatement($token)) {
                if ($token->statement->previous()) {
                    $token->statement->previous()->blankLineAfter();
                }

                $token->lineBreakAfter();
            }
        }
    }

    private function isAtStartOfStatement(mixed $token): bool
    {
        for ($index = 0; $index < $token->statement->getIndex($token); ++$index) {
            $earlierToken = $token->statement->getToken($index);

            if (!$earlierToken->inAttribute && !$earlierToken->is($this->tokenGroups->comments())) {
                return false;
            }
        }

        return true;
    }

    private function additionalLines(TokenTree $tree): void
    {
        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);
            $kind = $statement->declaration?->kind;
            $opens = $statement->lastToken()?->opensBlock;

            // The { of a class declaration on a line of its own, but not that of an anonymous class
            if ($kind?->isClassLike() && $opens === BlockType::ClassLike) {
                $statement->getToken(-2)->lineBreakAfter();
            }

            if ($kind === DeclarationKind::Function || $kind === DeclarationKind::Method) {
                // The { of the body on a line of its own, but not one that opens the hooks of a promoted property
                if ($opens === BlockType::Function) {
                    $statement->getToken(-2)->lineBreakAfter();
                }

                // An abstract or interface method
                elseif ($statement->lastToken()?->is(TokenType::SEMICOLON)) {
                    $statement->blankLineAfter();
                }
            }

            if ($type instanceof BlockCloser && !$statement->lastToken()->is(TokenType::CURLY_BRACKET_OPEN)) {
                // No blank lines after the } of a hook body like "get { }", which is in the hook list. Block closers
                // deeper inside hook bodies, like that of an if in "set { }", keep their blank lines.
                if ($statement->block->type === BlockType::HookList) {
                    continue;
                }

                $nextStatement = $statement->next();

                $closesLambda = $nextStatement?->firstToken()->is([
                    TokenType::SEMICOLON,
                    TokenType::COMMA,
                    TokenType::ROUND_BRACKET_CLOSE,
                ]);

                if ($closesLambda) {
                    // Lambda function body closer
                    $nextStatement->mergeWithPrevious();
                }

                $statement->blankLineAfter();
            }

            // No blank line needed before a case or default branch
            if ($type instanceof SwitchBranch && $previousStatement = $statement->previous()) {
                $previousStatement->blankLineAfter(false);
            }
        }
    }
}
