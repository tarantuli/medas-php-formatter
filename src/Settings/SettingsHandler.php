<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\Core\{Attributes\Service, Interfaces\ServiceManager};
use Medas\PhpFormatter\Formatters\Formatter;

#[Service]
readonly class SettingsHandler
{
    public function __construct(
        private ServiceManager $serviceManager,
    )
    {
    }

    /** @return Formatter[] */
    public function formatters(Settings $settings): array
    {
        $formatters = [];

        foreach ($settings->formatters as $formatter) {
            $formatters[] = $this->serviceManager->resolve($formatter);
        }

        foreach ($formatters as $formatter) {
            foreach ($formatter->additionalFormatters() as $additionalFormatter) {
                $formatters[] = $additionalFormatter;
            }
        }

        // Sort by priority, higher values first
        usort($formatters, fn(Formatter $a, Formatter $b) => -($a->priority() <=> $b->priority()));

        return $formatters;
    }
}
