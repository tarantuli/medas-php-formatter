<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Console\Formats\Color;
use Medas\Console\Printer;
use Medas\Console\Table;
use Medas\Console\Text;
use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\BlockDumper;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class OptionAssessmentDumper
{
    public function __construct(
        private BlockDumper $blockDumper,
        private Printer     $printer,
    )
    {
    }

    /** @param OptionAssessment[] $assessments */
    public function dump(Statement $statement, array $assessments): void
    {
        $table = Table::create(['Group', 'Depth', 'Lengths', 'Quality']);

        foreach ($assessments as $assessment) {
            $table->data[] = [
                $assessment->option->group->name,
                $assessment->option->depth,
                $assessment->lengths,
                (int) floor(1000 * $assessment->quality),
            ];
        }

        $this->blockDumper->dumpStatement($statement);

        if ($assessments) {
            $this->printer->print($table);
        }
        else {
            $this->printer->printLine(Text::create('no groups', Color::Gray));
        }
    }
}
