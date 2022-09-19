<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\Console\Formats\{BgColor, Color, HexColor};
use Medas\Console\Printer;
use Medas\PhpFormatter\Exceptions\NoConsolePrinterFoundException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlockDumper
{
    private int $line;

    public function __construct(
        private readonly Printer|null        $printer,
        private readonly StatementTypeFinder $typeFinder)
    {
    }

    public function dump(Block $block): void
    {
        if ($this->printer === null) {
            throw new NoConsolePrinterFoundException();
        }

        $this->line = 0;
        $this->printBlock($block);
        $this->printer->printEol();
    }

    private function printBlock(Block $block): void
    {
        foreach ($block as $statement) {
            // Start of line
            $this->printer->printEol()
                ->printText(sprintf('%3s', $this->line++), HexColor::create('#ff8700'))
                ->printText(str_repeat('·', $statement->block->depth), Color::LightGray);

            // Print tokens on this line
            foreach ($statement as $index => $token) {
                $this->printToken($index, $token);
            }

            // Print statement type
            $this->printer->printText('  ' . $this->typeFinder->for($statement), Color::Blue);

            if ($statement->blankLineAfter) {
                $this->printer->printText(' ⇊', HexColor::create('#d75fd7'));
            }

        }
    }

    private function printToken(int $index, Token $token): void
    {
        $this->printer->printText('|')
            ->printText((string) $index, Color::Blue)
            ->printText(':');

        $this->printer->printText((string) $token->context, HexColor::create('#878700'))
            ->printText(':');

        if ($token->inAttribute) {
            $this->printer->printText('A', HexColor::create('#d7d700'))
                ->printText(':');
        }

        if ($token->inString) {
            $this->printer->printText('S', HexColor::create('#d70000'))
                ->printText(':');
        }

        $this->printer->printText($token->getTokenName(), HexColor::create('#008700'));

        if ($token->getTokenName() !== $token->text) {
            $this->printer->printText('=');
            preg_match('/^(\s*)(.*?)(\s*)$/ms', $token->text, $parts);

            if (strlen($parts[1])) {
                $this->printer->printText($parts[1], BgColor::LightGray);
            }
            if (strlen($parts[2])) {
                $this->printer->printText($parts[2], Color::LightGray);
            }
            if (strlen($parts[3])) {
                $this->printer->printText($parts[3], BgColor::LightGray);
            }
        }

        if ($token->lineBreakAfter) {
            $this->printer->printText('↩', HexColor::create('#d75fd7'));
        }
        elseif ($token->spaceAfter) {
            $this->printer->printText('‿', HexColor::create('#d75fd7'));
        }
    }
}
