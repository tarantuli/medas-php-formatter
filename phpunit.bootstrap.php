<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\PhpFormatter\PhpFormatterPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackage(PhpFormatterPackage::instance())
    ->addPackage(ConfigManagerPackage::instance())
    ->addPackage(ConfigOptionsPackage::instance())
    ->addPackage(ConsolePrinterPackage::instance());

service(ConfigManager::class)
    ->readEnv(__DIR__)
    ->addDirectory(__DIR__ . '/tests/config');
