<?php

namespace Shared\Diff\Output;

use Shared\Diff\Interfaces\DiffOutputBuilderInterface;

abstract class AbstractChunkOutputBuilder implements DiffOutputBuilderInterface
{
    protected function getCommonChunks(array $diff, int $lineThreshold = 5): array
    {
        for ($i = 0; $i < $diffSize; ++$i) {
            /* OLD */
            if ($diff[$i][1] === 0) {
                if ($capturing === false) {
                    $capturing = true;
                    $chunkStart = $i;
                    $chunkSize = 0;
                }
                else {
                    ++$chunkSize;
                }
            }
        }
    }
}
