<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\{ClassAnalysis, ClassReference};
use Medas\PhpClassAnalysis\ReferenceFinder\TextAnalyzer;
use Medas\PhpTokenizer\{Token, TokenGroups, TokenTree};

#[Service]
readonly class ReferencesUpdater
{
    private array $nonReferencePrefixes;

    public function __construct(
        private TextAnalyzer $textAnalyzer,
        private TokenGroups  $tokenGroups,
    )
    {
        $this->nonReferencePrefixes = array_merge(
            $this->tokenGroups->structureTypes(),
            [T_OBJECT_OPERATOR, T_FUNCTION]
        );
    }

    public function update(TokenTree $tree, ClassAnalysis $analysis, ReferencesAndImports $referencesAndImports): void
    {
        $tree->doResetLinks();

        foreach ($tree as $token) {
            foreach ($analysis->uses as $reference) {
                if ($reference->label === $token->text
                        && !in_array($token->previous->id, $this->nonReferencePrefixes)) {
                    $token->text = $this->getNewText($referencesAndImports, $reference, $analysis);
                }

                if ($token->is(T_DOC_COMMENT)) {
                    $this->handleDoccomment($analysis, $token, $reference, $referencesAndImports);
                }
            }
        }
    }

    private function handleDoccomment(
        ClassAnalysis        $analysis,
        Token                $token,
        ClassReference       $reference,
        ReferencesAndImports $referencesAndImports
    ): void
    {
        if (!preg_match_all(
            '/@(?:param|var|return|throws)\s+((?:[^{}\s]+|\{[^}]*})+)/',
            $token->text,
            $matches,
            PREG_SET_ORDER
        )) {
            return;
        }

        foreach ($matches as $match) {
            $newLine = $match[0];

            foreach ($this->textAnalyzer->extractDocTypeNames($match[1]) as $tag) {
                if ($tag === $reference->label) {
                    $newLine = str_replace(
                        $tag,
                        $this->getNewText($referencesAndImports, $reference, $analysis),
                        $newLine
                    );
                }
            }

            if ($match[0] !== $newLine) {
                $token->text = str_replace($match[0], $newLine, $token->text);
            }
        }
    }

    private function getNewText(
        ReferencesAndImports $referencesAndImports,
        ClassReference       $reference,
        ClassAnalysis        $analysis
    ): string
    {
        $newText = $referencesAndImports->references[$reference->fqn];

        if (!$analysis->namespace && str_starts_with($newText, '\\')) {
            $newText = substr($newText, 1);
        }

        return $newText;
    }
}
