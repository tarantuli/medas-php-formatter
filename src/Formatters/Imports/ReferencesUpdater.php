<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\ClassAnalysis;
use Medas\PhpTokenizer\TokenTree;

#[Service]
class ReferencesUpdater
{
    public function update(TokenTree $tree, ClassAnalysis $analysis, ReferencesAndImports $referencesAndImports): void
    {
        $tree->doResetLinks();

        foreach ($tree as $token) {
            foreach ($analysis->uses as $reference) {
                if ($reference->label === $token->text) {
                    $token->text = $referencesAndImports->references[$reference->fqn];
                }
            }
        }
    }
}
