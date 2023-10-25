<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports\Grouping;

use Medas\Core\Attributes\ConfigValue;
use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\ConfigOptions\MaxImportGroupChildDepth;
use Medas\PhpFormatter\Formatters\Imports\ReferencesAndImports;

#[Service]
readonly class ImportGrouper
{
    public function __construct(
        #[ConfigValue(MaxImportGroupChildDepth::class)]
        private int $maxChildDepth,
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
                $instance->maxLevel = max($instance->maxLevel, substr_count($remainder, '\\'));
            }
        }
    }

    private function determineCandidates(Job $job): void
    {
        foreach ($job->prefixes as $prefix) {
            if ($prefix->count >= 2 && $prefix->maxLevel <= $this->maxChildDepth) {
                $job->candidates[] = $prefix;
            }
        }

        usort($job->candidates, fn(Prefix $a, Prefix $b) => -1 * ($a->count <=> $b->count));
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
