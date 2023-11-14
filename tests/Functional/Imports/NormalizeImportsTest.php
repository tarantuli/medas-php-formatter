<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class NormalizeImportsTest extends BaseTestClass
{
    public function testNormalize(): void
    {
        $settings = new Medas();
        $settings->import->maxRelativeDepth = 3;

        $this->assertChanges(
            'imports/normalize-imports-pre',
            'imports/normalize-imports-post',
            $settings
        );
    }

    public function testTryCatch(): void
    {
        $this->assertRemainsTheSame(
            'imports/try-catch',
            new Medas()
        );
    }

    public function testMultiExtends(): void
    {
        $this->assertChanges(
            'imports/multi-extends-pre',
            'imports/multi-extends-post',
            new Medas()
        );
    }
}
