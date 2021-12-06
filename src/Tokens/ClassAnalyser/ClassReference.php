<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

class ClassReference
{
    public function __construct(public string $reference,
                                public string $fqn
    )
    {
    }
}
