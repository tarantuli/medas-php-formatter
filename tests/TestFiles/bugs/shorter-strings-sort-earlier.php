<?php

declare(strict_types=1);

namespace Placeholder\Backend;

use Medas\Console\ConsolePackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;

class BackendPackage
{
    public function dependencies(): array
    {
        return [
            ConsolePackage::instance(),
            ConsolePrinterPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
