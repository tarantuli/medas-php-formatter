<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Formatters\TokenReplacements\UseExitInsteadOfDie;
use Medas\PhpBeautifier\Formatters\BlankLinesBeforeBlocks;
use Medas\PhpBeautifier\Formatters\BlankLinesBetweenClassSections;

class Medas extends Psr12
{
    public function __construct()
    {
        parent::__construct();

        $this->addFormatter(service(BlankLinesBetweenClassSections::class));
        $this->addFormatter(service(BlankLinesBeforeBlocks::class));
        $this->addFormatter(service(UseExitInsteadOfDie::class));
    }
}
