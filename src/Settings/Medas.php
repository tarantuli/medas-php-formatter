<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\BlockFormatters\ClassPartsBlankLines;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        $this->addBlockFormatter(service(ClassPartsBlankLines::class));
    }
}
