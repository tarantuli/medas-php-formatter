<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Service;

#[Service]
class ReferencesUpdater
{
    public function update(TokenTree $tree, ReferencesAndImports $referencesAndImports): void
    {
        $tree->doResetLinks();

        foreach ($tree as $token) {
            if (isset($token->reference)) {
                $token->text = $referencesAndImports->references[$token->reference->fqn];
            }
        }
    }
}
