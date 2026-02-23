<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class NoCommentsAtLineEndTest extends BaseTestClass
{
    public function testMoveCommentsToLineBefore(): void
    {
        $settings = new NoCommentsAtLineEndTestSettings();

        $this->assertChanges(
            'token-replacement/no-comments-at-line-end-pre',
            'token-replacement/no-comments-at-line-end-post',
            $settings
        );
    }
}
