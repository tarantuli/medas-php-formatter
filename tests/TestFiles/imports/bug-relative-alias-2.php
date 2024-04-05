<?php

declare(strict_types=1);

namespace Medas\FrameworkDocumentation\PackageDocGenerator\ClassProcessor\MethodProcessor;

use Medas\Core\{Attributes\Service, StringMaker};
use Medas\FrameworkDocumentation\Documentation\MethodDocs;
use Medas\FrameworkDocumentation\PackageDocGenerator;
use Medas\FrameworkDocumentation\PackageDocGenerator\ClassProcessor\TypeAliasNormalizer;
use Medas\FrameworkDocumentation\PackageDocGenerator\Job;
use Medas\PhpClassAnalysis\ClassAnalysis;

#[Service]
readonly class ParameterProcessor
{
    private StringMaker $stringMaker;

    public function __construct(
        private TypeAliasNormalizer $typeAliasNormalizer,
    )
    {
        $this->stringMaker = StringMaker::instance();
    }

    public function processParameter(
        Job                  $job,
        MethodDocs           $methodDocs,
        \ReflectionClass     $class,
        \ReflectionMethod    $method,
        \ReflectionParameter $parameter,
        ClassAnalysis|null   $analysis,
        false|string         $docComment
    ): void
    {
        if (in_array($name, PackageDocGenerator::ATTRIBUTES_TO_IGNORE, true)) {
            continue;
        }
    }
}
