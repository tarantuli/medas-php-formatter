<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\{ClassAnalysis, ClassReference};
use Medas\PhpTokenizer\{Token, TokenGroups, TokenTree};

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
                if ($reference->label === $token->text && !in_array(
                    $token->previous->id,
                    $this->tokenGroups->structureTypes()
                )) {
                    $token->text = $referencesAndImports->references[$reference->fqn];
                }

                if ($token->is(T_DOC_COMMENT)) {
                    $this->handleDoccomment($token, $reference, $referencesAndImports);
                }
            }
        }
    }

    private function handleDoccomment(
        Token                $token,
        ClassReference       $reference,
        ReferencesAndImports $referencesAndImports
    ): void
    {
        if (!preg_match_all('/@(?:param|var|return)\s+(\S+)/', $token->text, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            $newLine = $match[0];

            foreach (explode('|', $match[1]) as $tag) {
                if (str_ends_with($tag, '[]')) {
                    $tag = substr($tag, 0, -2);
                }

                if ($tag === $reference->label) {
                    $newLine = str_replace($tag, $referencesAndImports->references[$reference->fqn], $newLine);
                }
            }

            if ($match[0] !== $newLine) {
                $token->text = str_replace($match[0], $newLine, $token->text);
            }
        }
    }
}
