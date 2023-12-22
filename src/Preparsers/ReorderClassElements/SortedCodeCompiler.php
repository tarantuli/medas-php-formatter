<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;

#[Service]
readonly class SortedCodeCompiler
{
    public function compile(ReorderingJob $job): string
    {
        ob_start();

        $this->printCode($job);

        return ob_get_clean();
    }

    private function printCode(ReorderingJob $job): void
    {
        echo $job->leadingCode;

        foreach ($job->elements as $element) {
            echo $element->text;
        }

        echo $job->trailingCode;
    }
}
