<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Imports;

use Medas\PhpBeautifier\Tokens\ClassAnalyser\FqnProperties;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class NewImportsFinder
{
    public function __construct(
        private FqnProperties $fqnProperties,
    )
    {
    }

    public function determine(ReferencesAndImports $referencesAndImports, string $className)
    {
        foreach ($referencesAndImports->references as $fqn => $label) {
            if ($label !== null) {
                // There is already a new label for this FQN
                continue;
            }

            $lastPart = $this->fqnProperties->getLastPart($fqn);
            $alias = $lastPart;

            if (in_array($alias, $referencesAndImports->references) || $alias === $className) {
                // It's already a label for another FQN or the class name itself, prepend the next to last part
                $nextToLastPart = $this->fqnProperties->getNextToLastPart($fqn);
                $alias = $baseAlias = substr($nextToLastPart, 0, 3) . $lastPart;

                $counter = 0;
                while (in_array($alias, $referencesAndImports->references) || $alias === $className) {
                    $alias = $baseAlias . (++$counter);
                }
            }

            $referencesAndImports->references[$fqn] = $alias;
            $referencesAndImports->imports[$fqn] = $alias;
        }
    }
}
