<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\Helpers\StatementSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class FunctionDeclarationSplitter
{
    public function __construct(
        private StatementSplitter $splitter,
    )
    {
    }

    public function split(Statement $statement): bool
    {
        $openerIndex = null;
        $depth = 0;

        foreach ($statement as $index => $token) {
            if ($token->is(T_ROUND_BRACKET_OPEN)) {
                if ($openerIndex === null) {
                    $openerIndex = $index;
                }
                else {
                    ++$depth;
                }
            }

            if ($token->is((T_ROUND_BRACKET_CLOSE))) {
                if ($depth > 0) {
                    --$depth;
                }
                else {
                    $this->splitter->split($statement, [T_COMMA], true, $openerIndex, $index, 1);

                    return true;
                }
            }
        }

        return false;
    }
}
