<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\BlankLineAdder, Helpers\ReturnTypeTokens};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    Contexts\ClassBody,
    Contexts\MethodParameters,
    Contexts\PropertyHook,
    StatementTypeFinder,
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
    TokenGroups,
    TokenTree
};

#[Service]
readonly class Psr12BlankLines extends BaseFormatter
{
    public function __construct(
        private BlankLineAdder      $blankLineAdder,
        private ReturnTypeTokens    $returnTypeTokens,
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
                    if (!$token->context instanceof MethodParameters) {
                        $token->lineBreakAfter();
                    }
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

            if ($type instanceof ClassDeclaration) {
                $statement->getToken(-2)->lineBreakAfter();
            }

            if ($type instanceof FunctionDeclaration) {
                if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                    // Don't add a line break before { if the statement opens a promoted property hook.
                    // Use the same return-type-token walk-back as ReturnTypeTokens::isReturnTypeToken.
                    if ($this->returnTypeTokens->isReturnTypeToken($statement->lastToken())) {
                        $statement->getToken(-2)->lineBreakAfter();
                    }
                }
                else {
                    // It's an abstract function declaration
                    $statement->blankLineAfter();
                }
            }

            if ($type instanceof BlockCloser && !$statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                // Don't add blank lines for block closers that are direct children of a property
                // hook block (e.g. the } closing a "get { }" or "set { }" body). These are
                // identified by being in PropertyHook context whose parent block was opened by
                // a ClassBody-context statement (the property declaration).
                // Block closers deeper inside hook bodies (e.g. closing an "if" inside "set {}") are
                // NOT skipped, so blank lines within hook body code are preserved normally.
                if ($statement->firstToken()?->context instanceof PropertyHook
                        && $statement->block->opener?->firstToken()?->context instanceof ClassBody) {
                    continue;
                }

                $nextStatement = $statement->next();

                if ($nextStatement && $nextStatement->firstToken()->is([T_SEMICOLON, T_COMMA, T_ROUND_BRACKET_CLOSE])) {
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
