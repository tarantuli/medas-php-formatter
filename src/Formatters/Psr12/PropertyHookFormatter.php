<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\ReturnTypeTokens};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    Contexts\ClassBody,
    Contexts\MethodDeclaration,
    Contexts\PropertyHook,
    StatementTypeFinder,
    StatementTypes\BlockCloser,
    StatementTypes\ClassDeclaration,
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
            $firstToken = $statement->firstToken();

            $context = $firstToken !== null && isset($firstToken->context)
                ? $firstToken->context
                : null;

            $type = $this->typeFinder->for($statement);

            // Clear blank lines on promoted hook closer statements at class body level.
            // These are "}, " statements (start with }, end with ,) created by
            // PromotedPropertyHookSplitter. BlankLinesBetweenClassSections would otherwise
            // add a blank line between them and the next hook opener.
            if ($context instanceof ClassBody
                    && $statement->firstToken()?->is(T_CURLY_BRACKET_CLOSE)
                    && $statement->lastToken()?->is(T_COMMA)) {
                $statement->blankLineAfter(false);
            }

            // Any ClassBody statement ending with { that is not a function or class declaration
            // must be a property hook opener — either a regular property declaration
            // ("public string $name {") or a promoted hook continuation in a constructor
            // (", public int $age = 0 {"). Both need a space before { and no blank line after.
            if ($context instanceof ClassBody
                    && $statement->lastToken()->is(T_CURLY_BRACKET_OPEN)
                    && !$type instanceof FunctionDeclaration
                    && !$type instanceof ClassDeclaration) {
                $statement->getToken(-2)->spaceAfter();
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
            if ($context instanceof PropertyHook && $statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
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
            // DIRECT children of the hook block (i.e., closing get{}/set{} bodies).
            // Closers deeper inside hook bodies (e.g., closing an if inside set{}) are left
            // alone so that normal blank-line rules apply within hook body code.
            if ($context instanceof PropertyHook
                    && $type instanceof BlockCloser
                    && $statement->block->opener?->firstToken()?->context instanceof ClassBody) {
                $statement->blankLineAfter(false);
            }
        }
    }
}
