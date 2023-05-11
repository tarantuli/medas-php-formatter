<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\AsSingleton;
use Medas\FileSystem\FileSystemPackage;
use Medas\PhpClassAnalysis\PhpClassAnalysisPackage;
use Medas\PhpTokenizer\PhpTokenizerPackage;
use Medas\ServiceManager\BasePackage;

class PhpFormatterPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            PhpClassAnalysisPackage::class,
            PhpTokenizerPackage::class,
            FileSystemPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
