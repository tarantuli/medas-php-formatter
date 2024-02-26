<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Formatters\Replacements\{
    UseDieInsteadOfExit,
    UseExitInsteadOfDie,
    UseImplodeInsteadOfJoin,
    UseUnionNullInsteadOfNullable
};
use Medas\PhpFormatter\Settings\Psr12;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class TokenReplacementTest extends BaseTestClass
{
    public function testDieInsteadOfExit(): void
    {
        $settings = new Psr12();

        $settings->formatters[] = service(UseDieInsteadOfExit::class);

        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/die-instead-of-exit-post',
            $settings,
            'die() instead of exit()'
        );
    }

    public function testExitInsteadOfDie(): void
    {
        $settings = new Psr12();

        $settings->formatters[] = service(UseExitInsteadOfDie::class);

        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/exit-instead-of-die-post',
            $settings,
            'exit() instead of die()'
        );
    }

    public function testImplodeInsteadOfJoin(): void
    {
        $settings = new Psr12();

        $settings->formatters[] = service(UseImplodeInsteadOfJoin::class);

        $this->compare(
            'token-replacement/implode-and-join',
            'token-replacement/implode-instead-of-join-post',
            $settings,
            'implode() instead of join()'
        );
    }

    public function testUnionNullInsteadOfNullable(): void
    {
        $settings = new Psr12();

        $settings->formatters[] = service(UseUnionNullInsteadOfNullable::class);

        $this->compare(
            'token-replacement/union-null-pre',
            'token-replacement/union-null-post',
            $settings,
            'union null instead of nullable ?'
        );
    }
}
