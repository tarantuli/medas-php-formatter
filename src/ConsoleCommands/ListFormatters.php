<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder};
use Medas\PhpFormatter\Formatters\Formatter;

#[Service]
readonly class ListFormatters extends BaseConsoleCommand
{
    public function __construct(
        private PhpFormatterGroup $group,
        private ImplementorFinder $finder,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'list-formatters';
    }

    public function description(): string
    {
        return 'List all available formatters sorted by priority';
    }

    public function process(array $arguments): void
    {
        $formatters = $this->finder->find(Formatter::class);

        usort($formatters, fn(Formatter $a, Formatter $b) => $a->priority() <=> $b->priority());

        foreach ($formatters as $formatter) {
            printf("%3u %s\n", $formatter->priority(), $formatter->name());
        }
    }
}
