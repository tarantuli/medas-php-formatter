<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\FileSystem\TemporaryFiles;
use Medas\PhpReformatter\PhpReformatter;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackages([
    PhpReformatter::class,
    ConfigManager::class,
    TemporaryFiles::class,
]);

$sm->resolve(ConfigManager::class)
    ->readEnv(__DIR__)
    ->addDirectory(__DIR__ . '/config');
