<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Imports;

use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

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
