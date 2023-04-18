<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Settings\ImportSettings;
use Medas\PhpFormatter\Tokens\ClassAnalyser\ClassAnalysis;
use Medas\PhpFormatter\Tokens\ClassAnalyser\ClassReference;

#[Service]
class NewReferencesFinder
{
    public function determine(ClassAnalysis $analysis, ImportSettings $settings, ReferencesAndImports $referencesAndImports): void
    {
        foreach ($analysis->uses as $reference) {
            if (!$settings->importGlobalNamespace && $this->isGlobalReference($reference)) {
                // It's a global reference, and we don't want to import those
                $referencesAndImports->references[$reference->fqn] = $reference->fqn;
                continue;
            }

            if (null !== $depth = $this->getRelativeDepth($analysis, $reference)) {
                // It's a relative reference
                if ($settings->maxRelativeDepth >= 1 && $depth > $settings->maxRelativeDepth) {
                    // Too deep, import it
                    $referencesAndImports->references[$reference->fqn] = null;
                }
                else {
                    // A relative reference, inline it
                    $referencesAndImports->references[$reference->fqn] = $this->getRelativeReference($analysis, $reference);
                }

                continue;
            }

            if ($reference->label === $reference->fqn) {
                // It's an absolute reference, import it
                $referencesAndImports->references[$reference->fqn] = null;
                continue;
            }

            // Otherwise, it's an aliased reference, keep it
            if (!str_contains($reference->label, '\\')) {
                // The whole reference is an alias, keep it
                $referencesAndImports->references[$reference->fqn] = $reference->label;
                $referencesAndImports->imports[$reference->fqn] = $reference->label;
            }
            else {
                // The first part is an alias, the rest is relative to this
                $alias = substr($reference->label, 0, strpos($reference->label, '\\'));
                $aliasedPath = substr($reference->fqn, 0, -(strlen($reference->label) - strlen($alias)));
                $referencesAndImports->references[$reference->fqn] = $reference->label;
                $referencesAndImports->imports[$aliasedPath] = $alias;
            }
        }
    }

    private function isGlobalReference(ClassReference $reference): bool
    {
        return str_starts_with($reference->fqn, '\\') && !str_contains(substr($reference->fqn, 1), '\\');
    }

    private function getRelativeDepth(ClassAnalysis $analysis, ClassReference $reference): int|null
    {
        $relative = $this->getRelativeReference($analysis, $reference);

        return $relative === null ? null : substr_count($relative, '\\') + 1;
    }

    private function getRelativeReference(ClassAnalysis $analysis, ClassReference $reference): string|null
    {
        if (!str_starts_with($reference->fqn, '\\' . $analysis->namespace . '\\')) {
            return null;
        }

        return substr($reference->fqn, strlen($analysis->namespace) + 2);
    }

}
