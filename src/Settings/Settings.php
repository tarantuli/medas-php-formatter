<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\Formatter;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointSet;
use Medas\PhpFormatter\Formatters\Required\RequiredWhitespace;
use Medas\PhpFormatter\Preformatters\Preformatter;
use Medas\PhpFormatter\Preparsers\Preparser;
use Medas\PhpTokenizer\AdditionalTokensDefiner;

class Settings
{
    public DocumentSettings $document;
    public ImportSettings $import;

    // Breakpoint sets
    public BreakpointSet $classDeclarationSet;
    public BreakpointSet $controlStatementSet;
    public BreakpointSet $genericLineSet;
    public BreakpointSet $softLineSet;

    /** @var Preparser[] */
    private array $preparsers = [];

    /** @var Preformatter[] */
    private array $preformatters = [];

    /** @var Formatter[] */
    private array $formatters = [];

    public function __construct()
    {
        service(AdditionalTokensDefiner::class)->define();

        $this->document = new DocumentSettings();
        $this->import = new ImportSettings();

        $this->document->setLineEnding(new LineEndings\LineFeed())
            ->setIndentation(new Indentations\Spaces(4));

        $this->addFormatter(service(RequiredWhitespace::class));
    }

    public function addPreparser(Preparser $preparser): self
    {
        $this->preparsers[] = $preparser;

        return $this;
    }

    public function preparsers(): array
    {
        return $this->preparsers;
    }

    public function addPreformatter(Preformatter $preformatter): self
    {
        $this->preformatters[] = $preformatter;

        foreach ($preformatter->additionalFormatters() as $additionalFormatter) {
            $this->addFormatter($additionalFormatter);
        }

        return $this;
    }

    public function preformatters(): array
    {
        return $this->preformatters;
    }

    public function addFormatter(Formatter $formatter): self
    {
        $this->formatters[] = $formatter;

        foreach ($formatter->additionalFormatters() as $additionalFormatter) {
            $this->addFormatter($additionalFormatter);
        }

        return $this;
    }

    public function formatters(): array
    {
        // Sort by priority, higher values first
        usort(
            $this->formatters,
            fn(Formatter $a, Formatter $b) => -($a->priority() <=> $b->priority())
        );

        return $this->formatters;
    }
}
