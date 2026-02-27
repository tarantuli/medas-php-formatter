<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\AsSingleton;
use Medas\Core\BasePackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\PhpClassAnalysis\PhpClassAnalysisPackage;
use Medas\PhpTokenizer\PhpTokenizerPackage;

class PhpFormatterPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            PhpClassAnalysisPackage::instance(),
            PhpTokenizerPackage::instance(),
            FileSystemPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
