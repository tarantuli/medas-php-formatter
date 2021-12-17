<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Formatters\Formatter;
use Medas\PhpBeautifier\Formatters\RequiredWhitespace;
use Medas\PhpBeautifier\Preparsers\Preparser;
use Medas\PhpBeautifier\Settings\Indentations\Space;
use Medas\PhpBeautifier\Settings\LineEndings\LineFeed;

class Settings
{
    public DocumentSettings $document;
    public ImportSettings $import;

    /** @var Preparser[] */
    private array $preparsers = [];

    /** @var Formatter[] */
    private array $formatters = [];

    public function __construct()
    {
        $this->document = new DocumentSettings();
        $this->import = new ImportSettings();

        $this->document->setLineEnding(new LineFeed())
            ->setIndentation(new Space(4));

        $this->addFormatter(service(RequiredWhitespace::class));
    }

    public function addFormatter(Formatter $formatter): self
    {
        $this->formatters[] = $formatter;

        foreach ($formatter->additionalFormatters() as $additionalFormatter) {
            $this->addFormatter($additionalFormatter);
        }

        // Sort by priority, higher values first
        usort($this->formatters, fn(Formatter $a, Formatter $b) => -($a->priority() <=> $b->priority()));

        return $this;
    }

    public function addPreparser(Preparser $preparser): self
    {
        $this->preparsers[] = $preparser;

        foreach ($preparser->additionalFormatters() as $additionalFormatter) {
            $this->addFormatter($additionalFormatter);
        }

        return $this;
    }

    public function preparsers(): array
    {
        return $this->preparsers;
    }

    public function formatters(): array
    {
        return $this->formatters;
    }
}
