<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpTokenizer\{BlockDumper, TokenCollection, Tokenizer, TokenTree, TreeBuilder};

#[Service]
class Formatter
{
    private Settings\Settings $settings;
    private bool $dumpTree = false;

    public function __construct(
        private readonly BlockDumper   $blockDumper,
        private readonly BlockPrinter  $blockPrinter,
        private readonly CodeValidator $codeValidator,
        private readonly Tokenizer     $tokenizer,
        private readonly TreeBuilder   $treeBuilder,
    )
    {
    }

    public function format(string $code, Settings\Settings $settings): string
    {
        $this->assertCodeIsValid($code);

        $this->settings = $settings;

        $tokens = $this->tokenizer->tokenize($code);
        $this->applyPreparsers($tokens);

        $tree = $this->treeBuilder->fromCollection($tokens);
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
