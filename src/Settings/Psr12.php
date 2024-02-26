<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\{
    BlankLines\NoBlankLinesAtStatementEnd,
    Prs12\KeywordsToLowercase,
    Prs12\Psr12BlankLines,
    Prs12\Psr12ElseifWhileCatch,
    Prs12\Psr12VisibilityMarkers,
    Prs12\Psr12Whitespace,
    Replacements\SingleLineControlBodiesEncloser
};

class Psr12 extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setMaxLineLength(80);

        $this->formatters[] = service(KeywordsToLowercase::class);
        $this->formatters[] = service(NoBlankLinesAtStatementEnd::class);
        $this->formatters[] = service(Psr12BlankLines::class);
        $this->formatters[] = service(Psr12ElseifWhileCatch::class);
        $this->formatters[] = service(Psr12VisibilityMarkers::class);
        $this->formatters[] = service(Psr12Whitespace::class);
        $this->formatters[] = service(SingleLineControlBodiesEncloser::class);
    }
}
