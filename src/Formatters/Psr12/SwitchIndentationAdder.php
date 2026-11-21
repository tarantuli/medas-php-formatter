<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Block, BlockType, StatementTypeFinder, StatementTypes\SwitchBranch};

/**
 * The case and default lines of a switch and the statements below them are in the same block. This indents the
 * statements one level more than the case they belong to.
 */
#[Service]
readonly class SwitchIndentationAdder extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 460;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            // One level for each switch the statement is in, minus one for a case or default
            $statement->additionalDepth += $this->switchesAround($statement->block)
                - (int) ($this->typeFinder->for($statement) instanceof SwitchBranch);
        }
    }

    /**
     * The number of switch blocks this block is, or is inside, with braces or with the alternative syntax.
     */
    private function switchesAround(Block $block): int
    {
        $switches = 0;

        while (true) {
            if ($block->type === BlockType::Switch) {
                ++$switches;
            }

            $parent = $block->opener?->block;

            // The opener of the file block is the open tag, which is in the file block itself
            if ($parent === null || $parent === $block) {
                return $switches;
            }

            $block = $parent;
        }
    }
}
