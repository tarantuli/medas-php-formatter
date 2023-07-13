<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatter\Formatters\Imports\NormalizeImports;
use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class NormalizeImportsTest extends BaseTestClass
{
    public function testNormalize(): void
    {
        $settings = (new Medas())->addFormatter(service(NormalizeImports::class));
        $settings->import->maxRelativeDepth = 3;
        $this->assertChanges(
            'imports/normalize-imports-pre',
            'imports/normalize-imports-post',
            $settings
        );
    }
}
