<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\CodeValidator;

class ValidatorResult
{
    public function __construct(
        public bool        $isValid,
        public string|null $errorMessage = null,
    )
    {
    }
}
