<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\Core\{Attributes\Service, Interfaces\ServiceManager};
use Medas\PhpFormatter\{Formatters\Formatter, Preformatters\Preformatter};

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

        foreach ($settings->preformatters as $preformatterName) {
            /** @var Preformatter $preformatter */
            $preformatter = $this->serviceManager->resolve($preformatterName);

            foreach ($preformatter->additionalFormatters() as $additionalFormatter) {
                $formatters[] = $additionalFormatter;
            }
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
