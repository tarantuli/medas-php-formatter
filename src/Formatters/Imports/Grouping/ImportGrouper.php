<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports\Grouping;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\ConfigOptions\{MaxImportGroupChildDepth, MinImportGroupPrefixDepth};
use Medas\PhpFormatter\Formatters\Imports\ReferencesAndImports;

#[Service]
readonly class ImportGrouper
{
    public function __construct(
        #[ConfigValue(MaxImportGroupChildDepth::class)]
        private int $maxChildDepth,
        #[ConfigValue(MinImportGroupPrefixDepth::class)]
        private int $minPrefixDepth,
    )
    {
    }

    public function group(ReferencesAndImports $referencesAndImports): array
    {
        $job = new Job($referencesAndImports);

        $this->countPrefixes($job);
        $this->determineCandidates($job);
        $this->determineStatements($job);

        return $job->statements;
    }

    private function countPrefixes(Job $job): void
    {
        foreach ($job->referencesAndImports->imports as $fqn => $alias) {
            $offset = 0;

            while (false !== $offset = strpos($fqn, '\\', $offset + 1)) {
                $prefix = substr($fqn, 0, $offset);
                $remainder = substr($fqn, $offset);

                if (!isset($job->prefixes[$prefix])) {
                    $instance = $job->prefixes[$prefix] = new Prefix($prefix);
                }
                else {
                    $instance = $job->prefixes[$prefix];
                }

                ++$instance->count;

                $instance->maxChildDepth = max($instance->maxChildDepth, substr_count($remainder, '\\'));
            }
        }
    }

    private function determineCandidates(Job $job): void
    {
        foreach ($job->prefixes as $prefix) {
            if (
                $prefix->count >= 2
                && $prefix->maxChildDepth <= $this->maxChildDepth
                && $prefix->prefixDepth >= $this->minPrefixDepth
            ) {
                $job->candidates[] = $prefix;
            }
        }

        usort($job->candidates, function (Prefix $a, Prefix $b) {
            // Sort by count (higher count on top), then by prefix length (longer prefixes on top)
            if ($a->count === $b->count) {
                return -1 * ($a->length <=> $b->length);
            }

            return -1 * ($a->count <=> $b->count);
        });
    }

    private function determineStatements(Job $job): void
    {
        foreach ($job->referencesAndImports->imports as $fqn => $alias) {
            foreach ($job->candidates as $prefix) {
                if (str_starts_with($fqn, $prefix->prefix . '\\')) {
                    if (!array_key_exists($prefix->prefix, $job->statements)) {
                        $job->statements[$prefix->prefix] = [];
                    }

                    $job->statements[$prefix->prefix][substr($fqn, strlen($prefix->prefix) + 1)] = $alias;

                    continue 2;
                }
            }

            $job->statements[$fqn] = $alias;
        }
    }
}
