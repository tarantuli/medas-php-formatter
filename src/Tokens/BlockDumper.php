<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\Core\Cli;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlockDumper
{
    public function __construct(private Cli $cli)
    {
    }

    private int $line = 0;

    public function dump(Block $block): void
    {
        foreach ($block as $blockOrStatement) {
            if ($blockOrStatement instanceof Statement) {
                // Start of line
                $this->cli->print("\n")
                    ->print(sprintf('%3s',(string) $this->line++), Cli::COLOR256 . '208')
                    ->print(str_repeat('·', $blockOrStatement->block->depth), Cli::LIGHT_GRAY);

                // Print tokens on this line
                foreach ($blockOrStatement as $index => $token) {
                    $this->printToken($index, $token);
                }
            }
            else {
                $this->dump($blockOrStatement);
            }
        }
    }

    private function printToken(int $index, Token $token): void
    {
        $this->cli->print('|')
            ->print((string) $index, Cli::BLUE)
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
            preg_match('/^(\s*)(.*?)(\s*)$/', $token->text, $parts);

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
    }
}
