<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\Core\Cli;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlockDumper
{
    private int $line;

    public function __construct(
        private Cli                 $cli,
        private StatementTypeFinder $typeFinder)
    {
    }

    public function dump(Block $block): void
    {
        $this->line = 0;
        $this->printBlock($block);
        $this->cli->print("\n");
    }

    private function printBlock(Block $block): void
    {
        foreach ($block as $statement) {
            // Start of line
            $this->cli->print("\n")
                ->print(sprintf('%3s', $this->line++), Cli::COLOR256 . '208')
                ->print(str_repeat('·', $statement->block->depth), Cli::LIGHT_GRAY);

            // Print tokens on this line
            foreach ($statement as $index => $token) {
                $this->printToken($index, $token);
            }

            // Print statement type
            $this->cli->print('  ' . $this->typeFinder->for($statement), Cli::BLUE);

            if ($statement->blankLineAfter) {
                $this->cli->print(' ⇊', Cli::COLOR256 . '170');
            }

        }
    }

    private function printToken(int $index, Token $token): void
    {
        $this->cli->print('|')
            ->print((string) $index, Cli::BLUE)
            ->print(':');

        $this->cli->print((string) $token->context, Cli::COLOR256 . '100')
            ->print(':');

        if ($token->inAttribute) {
            $this->cli->print('A', Cli::COLOR256 . '184')
                ->print(':');
        }

        if ($token->inString) {
            $this->cli->print('S', Cli::COLOR256 . '160')
                ->print(':');
        }

        $this->cli->print($token->getTokenName(), Cli::COLOR256 . '28');

        if ($token->getTokenName() !== $token->text) {
            $this->cli->print('=');
            preg_match('/^(\s*)(.*?)(\s*)$/ms', $token->text, $parts);

            if (strlen($parts[1])) {
                $this->cli->print($parts[1], Cli::LIGHT_GRAY_BG);
            }
            if (strlen($parts[2])) {
                $this->cli->print($parts[2], Cli::LIGHT_GRAY);
            }
            if (strlen($parts[3])) {
                $this->cli->print($parts[3], Cli::LIGHT_GRAY_BG);
            }
        }

        if ($token->lineBreakAfter) {
            $this->cli->print('↩', Cli::COLOR256 . '170');
        }
        elseif ($token->spaceAfter) {
            $this->cli->print('‿', Cli::COLOR256 . '170');
        }
    }
}
