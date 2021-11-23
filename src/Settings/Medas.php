<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Formatters\BlankLinesBeforeBlocks;
use Medas\PhpBeautifier\Formatters\ClassPartsBlankLines;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        $this->addFormatter(service(ClassPartsBlankLines::class));
        $this->addFormatter(service(BlankLinesBeforeBlocks::class));
    }
}
