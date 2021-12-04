<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Formatters\Formatter;
use Medas\PhpBeautifier\Formatters\RequiredWhitespace;

class Settings
{
    public DocumentSettings $document;
    public ImportSettings $import;

    /** @var Formatter[] */
    private array $formatters = [];

    public function __construct()
    {
        $this->document = new DocumentSettings();
        $this->import = new ImportSettings();
        $this->addFormatter(service(RequiredWhitespace::class));
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
        return $this->formatters;
    }
}
