<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpBeautifier\Tokens\BlockDumper;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\Tokenizer;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Formatter
{
    private Settings\Settings $settings;
    private bool $dumpTree = false;

    public function __construct(
        private BlockDumper   $blockDumper,
        private BlockPrinter  $blockPrinter,
        private CodeValidator $codeValidator,
        private Tokenizer     $tokenizer,
    )
    {
    }

    public function format(string $code, Settings\Settings $settings): string
    {
        $this->assertCodeIsValid($code);

        $this->settings = $settings;

        $tokens = $this->tokenizer->tokenize($code);
        $this->applyPreparsers($tokens);

        $tree = $this->tokenizer->determineTree($tokens);
        $this->applyFormatters($tree);

        if ($this->dumpTree) {
            $this->blockDumper->dump($tree->block());
        }

        $result = $this->blockPrinter->print(
            $tree->block(),
            (string) $this->settings->document->indentation(),
            (string) $this->settings->document->lineEnding());

        $this->assertCodeIsValid($result);

        return $result;
    }

    private function assertCodeIsValid(string $code): void
    {
        if (!$this->codeValidator->validate($code)) {
            throw new ReformattedCodeIsInvalidException($code, $this->codeValidator->getErrorMessage());
        }
    }

    private function applyPreparsers(TokenCollection $tokens): void
    {
        foreach ($this->settings->preparsers() as $preparser) {
            $preparser->preparse($tokens);
        }
    }

    private function applyFormatters(TokenTree $tree): void
    {
        foreach ($this->settings->formatters() as $formatter) {
            $formatter->format($tree);
        }
    }

    public function settings(): Settings\Settings
    {
        return $this->settings;
    }
}
