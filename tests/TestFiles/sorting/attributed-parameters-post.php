<?php

namespace App;

use Medas\Core\Attributes\{ConfigValue, Service};

#[Service]
readonly class Locker
{
    public function __construct(
        private Repository $repository,

        #[ConfigValue(ConfigOptions\MarkerLength::class)]
        private int        $markerLength,

        #[ConfigValue(ConfigOptions\TaskClass::class)]
        private string     $taskClass,
        CodeGenerator      $codeGenerator,
    )
    {
    }
}
