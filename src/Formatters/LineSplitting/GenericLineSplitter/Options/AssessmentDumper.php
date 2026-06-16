<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Console\{Formats\SafeColor, Printer, Table, Text};
use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{BlockDumper, Statement};

#[Service]
readonly class AssessmentDumper
{
    public function __construct(
        private BlockDumper $blockDumper,
        private Printer     $printer,
    )
    {
    }

    /** @param Assessment[] $assessments */
    public function dump(Statement $statement, array $assessments): void
    {
        $table = Table::create(['Group', 'Depth', 'Indices', 'Lengths', 'Quality']);

        foreach ($assessments as $assessment) {
            $table->data[] = [
                $assessment->option->breakpointDefinition->name,
                $assessment->option->depth,
                implode(', ', $assessment->option->breakpointIndices),
                $assessment->lengths,
                (int) floor(1000 * $assessment->quality),
            ];
        }

        $this->blockDumper->dumpStatement($statement);
        $this->printer->printEol();

        if ($assessments) {
            $this->printer->print($table);
        }
        else {
            $this->printer->printLine(Text::create('no groups', SafeColor::Gray));
        }

        $this->printer->printEol();
    }
}
