<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\ClassAnalysis;
use Medas\PhpTokenizer\{TokenGroups, TokenTree};

#[Service]
readonly class ReferencesUpdater
{
    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
    }

    public function update(TokenTree $tree, ClassAnalysis $analysis, ReferencesAndImports $referencesAndImports): void
    {
        $tree->doResetLinks();

        foreach ($tree as $token) {
            foreach ($analysis->uses as $reference) {
                if ($reference->label === $token->text && !in_array($token->previous->id, $this->tokenGroups->structureTypes())) {
                    $token->text = $referencesAndImports->references[$reference->fqn];
                }
            }
        }
    }
}
