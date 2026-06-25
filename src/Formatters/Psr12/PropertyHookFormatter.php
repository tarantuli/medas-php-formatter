<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\ReturnTypeTokens};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    Block,
    Contexts\ClassBody,
    Contexts\MethodDeclaration,
    Contexts\PropertyHook,
    Statement,
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

        $this->collapseVirtualHookBlocks($job);
    }

    /**
     * Collapses property hook blocks that contain only bare virtual hook declarations
     * (get; and/or set; with no body) to the inline form: "public string $name { get; set; }".
     * This applies to interface properties and abstract class properties.
     *
     * Tree structure for "public string $fullName { get; }":
     *   Statement (ClassBody):  "public string $fullName {"   ← opener
     *   Block (PropertyHook):
     *     Statement:            "get;"                        ← virtual
     *   Statement (ClassBody):  "}"                           ← closer, in parent block
     *
     * StructureFinder places the } in the parent (class body) block after popping the
     * hook block off the stack. The } closer shares the same parent block as the opener,
     * so we find it by checking ClassBody } statements whose block matches the opener's block.
     * We use the hook block's opener reference to link them.
     */
    private function collapseVirtualHookBlocks(Job $job): void
    {
        // First pass: collect virtual-only hook blocks, keyed by opener object id.
        $blockGroups = [];

        foreach ($job->tree->statements() as $statement) {
            $context = $statement->firstToken()?->context ?? null;

            if (!$context instanceof PropertyHook) {
                continue;
            }

            // Only care about hook blocks whose opener is a ClassBody property declaration.
            if (!$statement->block->opener?->firstToken()?->context instanceof ClassBody) {
                continue;
            }

            $openerId = spl_object_id($statement->block->opener);

            if (!isset($blockGroups[$openerId])) {
                $blockGroups[$openerId] = [
                    'opener' => $statement->block->opener,
                    'block' => $statement->block,
                    'virtual' => [],
                    'closer' => null,
                    'isVirtualOnly' => true,
                ];
            }

            // A virtual hook statement ends with ; and has no arrow or block body.
            $isVirtual = $statement->lastToken()?->is(T_SEMICOLON)
                && !$statement->containsType(T_DOUBLE_ARROW)
                && !$statement->containsType(T_CURLY_BRACKET_OPEN);

            if (!$isVirtual) {
                $blockGroups[$openerId]['isVirtualOnly'] = false;

                continue;
            }

            $blockGroups[$openerId]['virtual'][] = $statement;
        }

        // Second pass: find the } closer for each virtual-only group.
        // The } is a ClassBody statement in the same parent block as the opener.
        // Its $statement->block->opener is the hook block's opener (our property declaration).
        // We match by checking if the statement's block is the opener's parent block AND
        // the hook block's opener === the property opener we recorded.
        foreach ($job->tree->statements() as $statement) {
            $context = $statement->firstToken()?->context ?? null;

            if (!$context instanceof ClassBody) {
                continue;
            }

            if (!$statement->firstToken()?->is(T_CURLY_BRACKET_CLOSE)) {
                continue;
            }

            // The } statement's block is the class body. Walk each group and check
            // whether this } belongs to one of our openers by comparing blocks.
            foreach ($blockGroups as &$group) {
                if ($group['closer'] !== null) {
                    continue;
                }

                // The opener and the } are siblings in the same parent block.
                if ($statement->block === $group['opener']->block) {
                    $group['closer'] = $statement;

                    break;
                }
            }

            unset($group);
        }

        foreach ($blockGroups as $group) {
            if (!$group['isVirtualOnly'] || $group['closer'] === null || $group['virtual'] === []) {
                continue;
            }

            $this->collapseToInline(
                $group['opener'],
                $group['virtual'],
                $group['block'],
                $group['closer']
            );
        }
    }

    /**
     * Merges virtual hook declarations onto the opener statement and removes the hook
     * block and its } closer to parent block, producing:
     * "public string $name { get; set; }"
     *
     * iterator_to_array() snapshots the token list before removal to avoid mutating
     * the generator mid-iteration.
     */
    private function collapseToInline(
        Statement $opener,
        array     $virtualStatements,
        Block     $hookBlock,
        Statement $closer
    ): void
    {
        $opener->findToken(T_CURLY_BRACKET_OPEN)->spaceAfter();

        foreach ($virtualStatements as $virtualStatement) {
            foreach (iterator_to_array($virtualStatement) as $token) {
                $virtualStatement->removeToken($token);

                $opener->appendToken($token);
            }
        }

        // Move the } token from the closer statement onto the opener.
        foreach (iterator_to_array($closer) as $token) {
            $closer->removeToken($token);

            $opener->appendToken($token);
        }

        // Remove the now-empty hook block and closer statement from the parent block.
        $opener->block->removeStatement($hookBlock);
        $opener->block->removeStatement($closer);

        if ($opener->next()) {
            $opener->blankLineAfter();
        }
    }
}
