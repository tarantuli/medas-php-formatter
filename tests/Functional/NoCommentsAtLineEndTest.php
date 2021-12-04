<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\Formatters\MoveCommentsAtLineEnd;
use Medas\PhpBeautifier\Formatters\NoCommentsAtLineEnd;
use Medas\PhpBeautifier\Settings\Psr12;

class NoCommentsAtLineEndTest extends BaseTest
{
    public function testMoveCommentsToLineBefore(): void
    {
        $this->assertChanges(
            'token-replacement/no-comments-at-line-end-pre',
            'token-replacement/no-comments-at-line-end-post',
            (new Psr12())->addFormatter(service(NoCommentsAtLineEnd::class))
                ->addFormatter(service(MoveCommentsAtLineEnd::class))
        );
    }
}
