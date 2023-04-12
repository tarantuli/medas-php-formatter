<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\PhpFormatter\Formatter;
use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpFormatter\Tokens\ClassAnalyser\ClassAnalyser;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Service;

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
class NormalizeImports extends BaseFormatter
{
    public function __construct(
        private readonly ClassAnalyser       $classAnalyser,
        private readonly Formatter           $formatter,
        private readonly NewImportsFinder    $newImportsFinder,
        private readonly NewImportsInserter  $newImportsInserter,
        private readonly NewReferencesFinder $newReferencesFinder,
        private readonly ReferencesUpdater   $referencesUpdater,
    )
    {
    }

    public function priority(): int
    {
        return 10;
    }

    public function format(TokenTree $tree): void
    {
        $analysis = $this->classAnalyser->analyseTokenCollection($tree);

        $referencesAndImports = new ReferencesAndImports();

        // Determine new references and new imports
        $this->newReferencesFinder->determine($analysis, $this->formatter->settings()->import, $referencesAndImports);
        $this->newImportsFinder->determine($referencesAndImports, $analysis->name);

        // Insert the new import header and update references in the body
        $this->newImportsInserter->insert($tree, $referencesAndImports);
        $this->referencesUpdater->update($tree, $referencesAndImports);
    }

}
