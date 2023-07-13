<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\ClassAnalyser;

use Medas\PhpClassAnalysis\ClassAnalyser;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ClassAnalyserTest extends BaseTestClass
{
    public function testClassAnalyser(): void
    {
        $analyser = service(ClassAnalyser::class);
        $code = file_get_contents(__DIR__ . '/../../TestFiles/imports/normalize-imports-pre.php');

        $results = $analyser->analyse($code);

        self::assertEquals('MyNamespace', $results->namespace);
        self::assertEquals('MyClass', $results->name);
        self::assertEquals('\MyNamespace\MyClass', $results->fqn);
        self::assertEquals('\MyNamespace\ChildNamespace\BaseClass', $results->extends->fqn);
        self::assertCount(2, $results->implements);
        self::assertTrue($results->isClass);
        self::assertCount(29, $results->uses);
    }
}
