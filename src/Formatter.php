<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\{ConfigValue, Entrypoint, HasMarkdownDocumentation, Service};
use Medas\PhpTokenizer\{AdditionalTokensDefiner, BlockDumper, Tokenizer, TreeBuilder};

#[Service, HasMarkdownDocumentation, Entrypoint]
readonly class Formatter
{
    public function __construct(
        AdditionalTokensDefiner          $additionalTokensDefiner,
        private BlockDumper              $blockDumper,
        private BlockPrinter             $blockPrinter,
        private CodeValidator            $codeValidator,
        private Settings\SettingsHandler $settingsHandler,
        private Tokenizer                $tokenizer,
        private TreeBuilder              $treeBuilder,

        #[ConfigValue(ConfigOptions\DumpParsedTree::class)]
        private bool                     $dumpParsedTree,

        #[ConfigValue(ConfigOptions\DumpResultTree::class)]
        private bool                     $dumpResultTree,

        #[ConfigValue(ConfigOptions\ValidateSourceCode::class)]
        private bool                     $validateSourceCode,

        #[ConfigValue(ConfigOptions\ValidateReformattedCode::class)]
        private bool                     $validateReformattedCode,
    )
    {
        $additionalTokensDefiner->define();
    }

    public function format(string $code, Settings\Settings $settings): string
    {
        $job = new Job($code, $settings);

        if ($this->validateSourceCode) {
            $result = $this->codeValidator->validate($job->code);

            if (!$result->isValid) {
                throw new Exceptions\SourceCodeIsInvalid($job->code, $result->errorMessage);
            }
        }

        $this->applyPreparsers($job);

        $job->tokens = $this->tokenizer->tokenize($job->code);

        $this->applyPreformatters($job);

        $job->tree = $this->treeBuilder->fromCollection($job->tokens);

        if ($this->dumpParsedTree) {
            $this->blockDumper->dump($job->tree->block());
        }

        $this->applyFormatters($job);

        if ($this->dumpResultTree) {
            $this->blockDumper->dump($job->tree->block());
        }

        $reformattedCode = $this->blockPrinter->print(
            $job->tree->block(),
            (string) $job->settings->document->indentation(),
            (string) $job->settings->document->lineEnding()
        );

        if ($this->validateReformattedCode) {
            $result = $this->codeValidator->validate($reformattedCode);

            if (!$result->isValid) {
                throw new Exceptions\ReformattedCodeIsInvalid(
                    $reformattedCode,
                    $result->errorMessage
                );
            }
        }

        return $reformattedCode;
    }

    private function applyPreparsers(Job $job): void
    {
        foreach ($job->settings->preparsers as $preparser) {
            $preparser->preparse($job);
        }
    }

    private function applyPreformatters(Job $job): void
    {
        foreach ($job->settings->preformatters as $preformatter) {
            $preformatter->preformat($job);
        }
    }

    private function applyFormatters(Job $job): void
    {
        foreach ($this->settingsHandler->formatters($job->settings) as $formatter) {
            $formatter->format($job);
        }
    }
}
