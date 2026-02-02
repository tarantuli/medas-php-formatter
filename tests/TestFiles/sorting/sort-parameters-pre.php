<?php

declare(strict_types=1);

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder, Interfaces\Serializer};
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

    public function __construct(
        private string  $namespace,
        Serializer|null $serializer = null,
    )
    {
    }

    public function __construct(
        Serializer|null $serializer,
        private string  $namespace,
    )
    {
    }
}
