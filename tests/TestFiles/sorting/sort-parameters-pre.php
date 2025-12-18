<?php

declare(strict_types=1);

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder};
use Medas\PhpFormatter\ConsoleCommands\PhpFormatterGroup;

#[Service]
readonly class SortParamters extends BaseConsoleCommand
{
    public function __construct(
        private PhpFormatterGroup $group,
        private ImplementorFinder $finder,
    )
    {
    }
}
