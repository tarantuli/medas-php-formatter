<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\ClassAnalyser;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

/**
 * This service normalizes all imports in a file.
 *
 * Basic rules:
 * If an import is aliased, that alias is used whenever possible.
 * If a reference is absolute, it's imported.
 * If a reference is relative, it's inlined.
 *
 * Settings dependant rules:
 * If a class is in the global namespace, and settings->importSettings->importGlobalNamespace is false,
 *    the reference is inlined instead of imported.
 * If a reference is relative, but deeper than settings->importSettings->maxRelativeDepth,
 *    the reference is imported instead of inlined.
 */
#[Service]
readonly class NormalizeImports extends BaseFormatter
{
    public function __construct(
        private ClassAnalyser       $classAnalyser,
        private NewImportsFinder    $newImportsFinder,
        private NewImportsInserter  $newImportsInserter,
        private NewReferencesFinder $newReferencesFinder,
        private ReferencesUpdater   $referencesUpdater,
    )
    {
    }

    public function priority(): int
    {
        return 1600;
    }

    public function format(Job $job): void
    {
        $analysis = $this->classAnalyser->analyseTokenTree($job->tree);
        $referencesAndImports = new ReferencesAndImports();

        // Determine new references and new imports
        $this->newReferencesFinder->determine(
            $analysis,
            $job->settings->import,
            $referencesAndImports
        );

        $this->newImportsFinder->determine($referencesAndImports, $analysis->name);

        // Insert the new import header and update references in the body
        $this->newImportsInserter->insert($job->tree, $referencesAndImports);
        $this->referencesUpdater->update($job->tree, $analysis, $referencesAndImports);
    }
}
