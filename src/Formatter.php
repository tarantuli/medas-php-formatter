<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpBeautifier\Formatters\Phases\AfterDeterminingContext;
use Medas\PhpBeautifier\Formatters\Phases\BeforeStrippingWhitespace;
use Medas\PhpBeautifier\Tokens\BlockDumper;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\Tokenizer;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Formatter
{
    private TokenCollection $tokens;
    private Settings\Settings $settings;

    public function __construct(
        private Tokenizer $tokenizer,
        private BlockPrinter $blockPrinter,
    )
    {
    }

    /** @noinspection RedundantSuppression */
    public function format(string $code, Settings\Settings $settings): string
    {
        $this->tokens = $this->tokenizer->tokenize($code, determineStructureAndContext: false);
        $this->settings = $settings;

        $this->applyFormatters(BeforeStrippingWhitespace::class);
        $this->tokenizer->determineStructureAndContext($this->tokens);
        $this->applyFormatters(AfterDeterminingContext::class);

        if (false) {
            /** @noinspection PhpUnreachableStatementInspection */
            service(BlockDumper::class)->dump($this->tokens->structure);
        }

        $result = $this->blockPrinter->print(
            $this->tokens->structure,
            (string) $this->settings->document->indentation(),
            (string) $this->settings->document->lineEnding());

        $this->assertCodeIsValid($result);

        return $result;
    }

    private function applyFormatters(string $phase): void
    {
        foreach ($this->settings->formatters() as $formatter) {
            if ($formatter->applyWhen() instanceof $phase) {
                $formatter->format($this->tokens);
            }
        }
    }

    private function assertCodeIsValid(string $code): void
    {
        $validator = service(CodeValidator::class);

        if (!$validator->validate($code)) {
            throw new ReformattedCodeIsInvalidException($code, $validator->getErrorMessage());
        }
    }

    public function settings(): Settings\Settings
    {
        return $this->settings;
    }
}
