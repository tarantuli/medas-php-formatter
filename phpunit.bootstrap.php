<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\PhpFormatter\PhpFormatterPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackage(PhpFormatterPackage::instance());

service(ConfigManager::class)
    ->readEnv(__DIR__)
    ->addDirectory(__DIR__ . '/config');
