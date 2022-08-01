<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Core\CorePackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\BasePackage;

class PhpFormatterPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            ConfigManagerPackage::class,
            ConfigOptionsPackage::class,
            CorePackage::class,
            FileSystemPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
