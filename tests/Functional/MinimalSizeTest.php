<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\MinimalSize;

class MinimalSizeTest extends BaseTestClass
{
    public function testRequiredWhitespace(): void
    {
        $this->assertChanges('basic-preformatted', 'basic-preformatted-minimal-whitespace', new MinimalSize());
    }
}
