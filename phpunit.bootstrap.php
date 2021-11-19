<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\Core\Cli;
use Medas\FileSystem\TemporaryFiles;
use Medas\PhpBeautifier\PhpBeautifier;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackages([
    PhpBeautifier::class,
    ConfigManager::class,
    TemporaryFiles::class,
    Cli::class,
]);

service(ConfigManager::class)
    ->readEnv(__DIR__)
    ->addDirectory(__DIR__ . '/config');
