<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Block, BlockType, DeclarationKind, Statement, TokenType};

#[Service]
readonly class PropertyHookFormatter extends BaseFormatter
{
    public function priority(): int
    {
        // Run after all blank line formatters (900+) and whitespace formatters (1200),
        // so we can clean up their effects on property hook declarations and hook blocks.
        return 90;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            $first = $statement->firstToken();
            $last = $statement->lastToken();

            // A "}," that closes the hook list of a promoted property, as split by PromotedPropertyHookSplitter.
            // BlankLinesBetweenClassSections would otherwise add a blank line before the next parameter.
            if ($first?->is(TokenType::CURLY_BRACKET_CLOSE)
                    && $first->matchingBracket?->opensBlock === BlockType::HookList
                    && $last->is(TokenType::COMMA)) {
                $statement->blankLineAfter(false);
            }

            // The opener of a hook list, like "public string $name {", ", public int $age = 0 {" or
            // "public function __construct(public string $name {": a space before { and no blank line after it
            if ($last?->opensBlock === BlockType::HookList) {
                $statement->getToken(-2)->spaceAfter();
                $statement->blankLineAfter(false);
            }

            // The opener of a hook body, like "get {" or "set (string $value) {"
            if ($last?->opensBlock === BlockType::HookBody) {
                $statement->getToken(-2)->spaceAfter();
            }

            // No blank lines between the hooks, also when a formatter that ran earlier added them
            if ($statement->block->type === BlockType::HookList) {
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
     *   Statement:              "public string $fullName {"   ← opener
     *   Block (HookList):
     *     Statement:            "get;"                        ← virtual
     *   Statement:              "}"                           ← closer, in the class body
     *
     * The closer is found through the matching bracket of the opener's {.
     */
    private function collapseVirtualHookBlocks(Job $job): void
    {
        // The hook lists of properties, by object id
        $hookLists = [];

        foreach ($job->tree->statements() as $statement) {
            $block = $statement->block;

            if ($block->type === BlockType::HookList && $this->isPropertyHookList($block)) {
                $hookLists[spl_object_id($block)] = $block;
            }
        }

        foreach ($hookLists as $hookList) {
            $virtualStatements = [];

            foreach ($hookList as $element) {
                if (!$element instanceof Statement || !$this->isVirtualHook($element)) {
                    continue 2;
                }

                $virtualStatements[] = $element;
            }

            $closer = $hookList->opener->lastToken()->matchingBracket?->statement;

            if ($virtualStatements !== [] && $closer !== null) {
                $this->collapseToInline($hookList->opener, $virtualStatements, $hookList, $closer);
            }
        }
    }

    /**
     * The hooks of a property declared in the class body, not of a promoted constructor parameter.
     */
    private function isPropertyHookList(Block $hookList): bool
    {
        $opener = $hookList->opener;

        return $opener->block->type === BlockType::ClassLike
            && $opener->declaration?->kind !== DeclarationKind::Method;
    }

    /**
     * A virtual hook ends with ; and has no arrow or block body.
     */
    private function isVirtualHook(Statement $statement): bool
    {
        return $statement->lastToken()?->is(TokenType::SEMICOLON)
            && !$statement->containsType(T_DOUBLE_ARROW)
            && !$statement->containsType(TokenType::CURLY_BRACKET_OPEN);
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
        $opener->lastToken()->spaceAfter();

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
