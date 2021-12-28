<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\Core\CorePackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\BasePackage;

class PhpFormatterPackage extends BasePackage
{
    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigManagerPackage::class,
            CorePackage::class,
            FileSystemPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
