<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpClassAnalysis\{ClassAnalysis, FqnProperties};
use Medas\PhpFormatter\ConfigOptions\MaxImportGroupChildDepth;

#[Service]
readonly class NewImportsFinder
{
    public function __construct(
        private FqnProperties $fqnProperties,

        #[ConfigValue(MaxImportGroupChildDepth::class)]
        private int           $maxChildDepth,
    )
    {
    }

    public function determine(ReferencesAndImports $referencesAndImports, ClassAnalysis $analysis): void
    {
        foreach ($referencesAndImports->references as $fqn => $label) {
            if ($label !== null) {
                foreach($referencesAndImports->references as $otherFqn => $otherLabel) {
                    if ($fqn === $otherFqn) {
                        continue;
                    }

                    if (str_starts_with($label, substr($otherFqn, 1) . '\\')) {
                        // There's another import that this is a child of
                        $referencesAndImports->references[$fqn] = $otherLabel . substr($fqn, strlen($otherFqn));
                    }
                }
                // There is already a new label for this FQN
                continue;
            }

            $lastPart = $this->fqnProperties->getLastPart($fqn);
            $doImport = true;

            if (str_starts_with($fqn, '\\' . $analysis->namespace . '\\')) {
                $alias = substr($fqn, strlen($analysis->namespace) + 2);

                if (substr_count($alias, '\\') <= $this->maxChildDepth) {
                    $doImport = false;
                }
                else {
                    $alias = $lastPart;
                }
            }
            else {
                $alias = $lastPart;
            }

            if (in_array($alias, $referencesAndImports->references) || $alias === $analysis->name) {
                // It's already a label for another FQN or the class name itself, prepend the next-to-last part
                $nextToLastPart = $this->fqnProperties->getNextToLastPart($fqn);
                $alias = $baseAlias = substr($nextToLastPart, 0, 5) . $lastPart;
                $counter = 0;

                while (in_array($alias, $referencesAndImports->references) || $alias === $analysis->name) {
                    $alias = $baseAlias . (++$counter);
                }
            }

            $referencesAndImports->references[$fqn] = $alias;

            if ($doImport) {
                $referencesAndImports->imports[$fqn] = $alias;
            }
        }
    }
}
