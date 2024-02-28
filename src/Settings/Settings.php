<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\Formatter;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointSet;
use Medas\PhpFormatter\Formatters\Required\RequiredWhitespace;
use Medas\PhpFormatter\Preformatters\Preformatter;
use Medas\PhpFormatter\Preparsers\Preparser;

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
    public array $preparsers = [];

    /** @var Preformatter[] */
    public array $preformatters = [];

    /** @var Formatter[] */
    public array $formatters = [];

    public function __construct()
    {
        $this->document = new DocumentSettings();
        $this->import = new ImportSettings();

        $this->document->setLineEnding(new LineEndings\LineFeed())
            ->setIndentation(new Indentations\Spaces(4));

        $this->formatters[] = service(RequiredWhitespace::class);
    }
}
