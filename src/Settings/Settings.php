<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointSet;
use Medas\PhpFormatter\Formatters\Required\RequiredWhitespace;

readonly class Settings
{
    public DocumentSettings $document;
    public ImportSettings $import;

    // Breakpoint sets
    public BreakpointSet|null $classDeclarationSet;
    public BreakpointSet|null $controlStatementSet;
    public BreakpointSet|null $genericLineSet;
    public BreakpointSet|null $softLineSet;

    /**
     * An array of Preparser class names
     *
     * @var string[]
     */
    public array $preparsers;

    /**
     * An array of Preformatter class names
     *
     * @var string[]
     */
    public array $preformatters;

    /**
     * An array of Formatter class names
     *
     * @var string[]
     */
    public array $formatters;

    public function __construct(
        array              $additionalFormatters = [],
        array              $additionalPreparsers = [],
        array              $additionalPreformatters = [],
        BreakpointSet|null $classDeclarationSet = null,
        BreakpointSet|null $controlStatementSet = null,
        BreakpointSet|null $genericLineSet = null,
        BreakpointSet|null $softLineSet = null,
    )
    {
        $document = new DocumentSettings();

        $document->setLineEnding(new LineEndings\LineFeed())
            ->setIndentation(new Indentations\Spaces(4));

        $this->document = $document;
        $this->import = new ImportSettings();
        $this->preparsers = $additionalPreparsers;
        $this->preformatters = $additionalPreformatters;

        $this->formatters = [
            RequiredWhitespace::class,
            ...$additionalFormatters,
        ];

        $this->classDeclarationSet = $classDeclarationSet;
        $this->controlStatementSet = $controlStatementSet;
        $this->genericLineSet = $genericLineSet;
        $this->softLineSet = $softLineSet;
    }
}
