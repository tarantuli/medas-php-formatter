<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\Prs12\{KeywordsToLowercase,
    Psr12BlankLines,
    Psr12ElseifWhileCatch,
    Psr12VisibilityMarkers,
    Psr12Whitespace
};
use Medas\PhpFormatter\Formatters\BlankLines\NoBlankLinesAtStatementEnd;
use Medas\PhpFormatter\Formatters\Replacements\NoSingleLineControlBodies;

class Psr12 extends Settings
{
    public function __construct()
    {
        parent::__construct();
        $this->document->setMaxLineLength(80);

        $this->addFormatter(service(NoSingleLineControlBodies::class));
        $this->addFormatter(service(KeywordsToLowercase::class));
        $this->addFormatter(service(Psr12VisibilityMarkers::class));
        $this->addFormatter(service(Psr12ElseifWhileCatch::class));
        $this->addFormatter(service(Psr12BlankLines::class));
        $this->addFormatter(service(Psr12Whitespace::class));
        $this->addFormatter(service(NoBlankLinesAtStatementEnd::class));
    }
}
