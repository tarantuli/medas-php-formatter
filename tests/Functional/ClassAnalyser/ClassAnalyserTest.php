<?php

declare(strict_types=1);

namespace Medas\Test\Functional\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\ClassAnalyser\ClassAnalyser;
use Medas\Test\Functional\BaseTest;

class ClassAnalyserTest extends BaseTest
{
    public function testClassAnalyser(): void
    {
        $analyser = service(ClassAnalyser::class);
        $code = file_get_contents(__DIR__ . '/../../TestFiles/imports/normalize-imports-pre.php');

        $results = $analyser->analyse($code);

        diedump($results->uses);
    }
}
