<?php

declare(strict_types=1);

namespace Medas\Test\Functional\Imports;

use Medas\PhpBeautifier\Formatters\Imports\NormalizeImports;
use Medas\PhpBeautifier\Settings\Medas;
use Medas\Test\Functional\BaseTest;

class NormalizeImportsTest extends BaseTest
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
