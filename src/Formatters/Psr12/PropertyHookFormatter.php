<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Helpers\ReturnTypeTokens, Job};
use Medas\PhpTokenizer\{
    Contexts\ClassBody,
    Contexts\MethodDeclaration,
    Contexts\PropertyHook,
    StatementTypeFinder,
    StatementTypes\BlockCloser,
    StatementTypes\ClassPropertyDeclaration,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class PropertyHookFormatter extends BaseFormatter
{
    public function __construct(
        private ReturnTypeTokens    $returnTypeTokens,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        // Run after all blank line formatters (900+) and whitespace formatters (1200),
        // so we can clean up their effects on property hook declarations and hook blocks.
        return 90;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            $context = $statement->firstToken()?->context;
            $type = $this->typeFinder->for($statement);

            // Property hook declaration in a class body:
            // e.g. "public string $name {" or "public string $name = 'default' {"
            // Also handles subsequent promoted property hooks in constructors, which appear
            // as ClassPropertyDeclaration after the first hook block closes.
            if ($context instanceof ClassBody
                    && $type instanceof ClassPropertyDeclaration
                    && $statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                // Ensure space before the opening { (e.g. "$name {" not "$name{")
                $statement->getToken(-2)->spaceAfter();

                // Remove any blank line added after this declaration by BlankLinesBetweenClassSections —
                // the blank line goes after the closing }, not after the opening {.
                $statement->blankLineAfter(false);
            }

            // Promoted property hook opening inside a FunctionDeclaration:
            // e.g. "public function __construct(public string $name {" or
            // "public function __construct(public string $email = 'user@example.com' {"
            // Detected by the same walk-back logic used in ContextAdder.
            if ($context instanceof MethodDeclaration
                    && $type instanceof FunctionDeclaration
                    && $statement->lastToken()->is(T_CURLY_BRACKET_OPEN)
                    && !$this->returnTypeTokens->isReturnTypeToken($statement->lastToken())) {
                $statement->getToken(-2)->spaceAfter();
                $statement->blankLineAfter(false);
            }

            // Inside a property hook block: add space before { for long-form hooks.
            // e.g. "get {" or "set {"
            if ($context instanceof PropertyHook
                    && $statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                $statement->getToken(-2)->spaceAfter();
            }

            // Clear blankLineAfter from all direct hook-block-level statements.
            // Any formatter that ran earlier may have added a blank line between hooks
            // (get/set declarations); this ensures none survives into the output.
            if ($context instanceof PropertyHook
                    && $statement->block->opener?->firstToken()?->context instanceof ClassBody) {
                $statement->blankLineAfter(false);
            }

            // Inside a property hook block: remove blank lines after block closers that are
            // DIRECT children of the hook block (i.e. closing get{}/set{} bodies).
            // Closers deeper inside hook bodies (e.g. closing an if inside set{}) are left
            // alone so that normal blank-line rules apply within hook body code.
            if ($context instanceof PropertyHook
                    && $type instanceof BlockCloser
                    && $statement->block->opener?->firstToken()?->context instanceof ClassBody) {
                $statement->blankLineAfter(false);
            }
        }
    }
}
