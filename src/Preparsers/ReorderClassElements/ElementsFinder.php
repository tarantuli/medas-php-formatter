<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\TokenGroups;

#[Service]
readonly class ElementsFinder
{
    private array $structureTypes;

    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
        $this->structureTypes = $this->tokenGroups->structureTypes();
    }

    public function find(ReorderingJob $job): void
    {
        foreach ($job->tokens as $index => $token) {
            if ($token->text === '"') {
                $job->inString = !$job->inString;
            }

            if ($job->inString && in_array($token->id, ['{', '}'])) {
                $token->id = T_STRING;
            }

            // Increase current brace and parenthesis depths if needed
            if ($token->text === '{') {
                ++$job->currentBraceDepth;
            }
            elseif ($token->text === '(') {
                ++$job->currentParenthesisDepth;
            }

            if (!$job->inClassCode) {
                if (in_array($token->id, $this->structureTypes, true)
                        && $job->tokens[$index - 1]->id !== T_DOUBLE_COLON) {
                    // Class statement
                    $job->classOpeningBraceAtParenthesisDepth = $job->currentParenthesisDepth;
                    $job->leadingCode .= $token->text;
                }
                elseif ($job->inTrailingCode) {
                    // We're in trailing code
                    $job->trailingCode .= $token->text;
                }
                elseif (
                    $job->classOpeningBraceAtParenthesisDepth !== null
                    && $token->text === '{'
                    && $job->currentParenthesisDepth === $job->classOpeningBraceAtParenthesisDepth
                ) {
                    // This is the class opening brace
                    $job->classBraceDepth = $job->currentBraceDepth;
                    $job->classParenthesisDepth = $job->currentParenthesisDepth;
                    $job->inClassCode = true;
                    $job->leadingCode .= $token->text;
                }
                else {
                    // We're still in leading code
                    $job->leadingCode .= $token->text;
                }
            }
            else {
                // We're in the class definition
                if ($token->text === '}' && $job->currentBraceDepth === $job->classBraceDepth) {
                    // This is the class closing brace
                    $job->inClassCode = false;
                    $job->inTrailingCode = true;
                    $job->trailingCode .= $token->text;
                }
                elseif (!$job->inStatement) {
                    if ($token->id === T_WHITESPACE) {
                        $job->elements[$job->elementIndex]->text .= $token->text;
                    }
                    else {
                        // Start a new statement
                        $job->elements[++$job->elementIndex] = new Element();
                        $job->elements[$job->elementIndex]->text = $token->text;
                        $job->inStatement = true;
                    }
                }
                else {
                    $job->elements[$job->elementIndex]->text .= $token->text;

                    // End of statement?
                    if ($token->text === ';'
                            && $job->currentBraceDepth === $job->classBraceDepth
                            && $job->currentParenthesisDepth === $job->classParenthesisDepth) {
                        $job->inStatement = false;
                    }

                    if ($token->text === '}'
                            && $job->currentBraceDepth - 1 === $job->classBraceDepth
                            && $job->currentParenthesisDepth === $job->classParenthesisDepth) {
                        $job->inStatement = false;
                    }
                }
            }

            // Decrease current brace and parenthesis depths if needed
            if ($token->text === '}') {
                --$job->currentBraceDepth;
            }
            elseif ($token->text === ')') {
                --$job->currentParenthesisDepth;
            }
        }
    }
}
