<?php

declare(strict_types=1);

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder, Interfaces\Serializer};
use Medas\PhpFormatter\ConsoleCommands\PhpFormatterGroup;

#[Service]
readonly class SortParamters extends BaseConsoleCommand
{
    public function __construct(
        private ImplementorFinder $finder,
        private PhpFormatterGroup $group,
    )
    {
    }

    public function __construct(
        private string  $namespace,
        Serializer|null $serializer = null,
    )
    {
    }

    public function __construct(
        private string  $namespace,
        Serializer|null $serializer,
    )
    {
    }
}
