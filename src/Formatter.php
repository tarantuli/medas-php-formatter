<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\ConfigOptions\{DumpParsedTree, DumpResultTree};
use Medas\PhpFormatter\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpTokenizer\{BlockDumper, Tokenizer, TreeBuilder};

#[Service]
readonly class Formatter
{
    public function __construct(
        private BlockDumper   $blockDumper,
        private BlockPrinter  $blockPrinter,
        private CodeValidator $codeValidator,
        private Tokenizer     $tokenizer,
        private TreeBuilder   $treeBuilder,

        #[ConfigValue(DumpParsedTree::class)]
        private bool          $dumpParseTree,

        #[ConfigValue(DumpResultTree::class)]
        private bool          $dumpResultTree,
    )
    {
    }

    public function format(string $code, Settings\Settings $settings): string
    {
        $job = new Job($settings);
        $this->assertCodeIsValid($code);

        $job->tokens = $this->tokenizer->tokenize($code);
        $this->applyPreparsers($job);

        $job->tree = $this->treeBuilder->fromCollection($job->tokens);

        if ($this->dumpParseTree) {
            $this->blockDumper->dump($job->tree->block());
        }

        $this->applyFormatters($job);

        if ($this->dumpResultTree) {
            $this->blockDumper->dump($job->tree->block());
        }

        $result = $this->blockPrinter->print(
            $job->tree->block(),
            (string) $job->settings->document->indentation(),
            (string) $job->settings->document->lineEnding());

        $this->assertCodeIsValid($result);

        return $result;
    }

    private function assertCodeIsValid(string $code): void
    {
        if (!$this->codeValidator->validate($code)) {
            throw new ReformattedCodeIsInvalidException($code, $this->codeValidator->getErrorMessage());
        }
    }

    private function applyPreparsers(Job $job): void
    {
        foreach ($job->settings->preparsers() as $preparser) {
            $preparser->preparse($job);
        }
    }

    private function applyFormatters(Job $job): void
    {
        foreach ($job->settings->formatters() as $formatter) {
            $formatter->format($job);
        }
    }
}
