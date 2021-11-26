<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\Formatters\Replacements\UseDieInsteadOfExit;
use Medas\PhpBeautifier\Formatters\Replacements\UseExitInsteadOfDie;
use Medas\PhpBeautifier\Formatters\Replacements\UseImplodeInsteadOfJoin;
use Medas\PhpBeautifier\Settings\Psr12;

class TokenReplacementTest extends BaseTest
{
    public function testDieInsteadOfExit(): void
    {
        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/die-instead-of-exit-post',
            (new Psr12())->addFormatter(service(UseDieInsteadOfExit::class)),
            'die() instead of exit()'
        );
    }

    public function testExitInsteadOfDie(): void
    {
        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/exit-instead-of-die-post',
            (new Psr12())->addFormatter(service(UseExitInsteadOfDie::class)),
            'exit() instead of die()'
        );
    }

    public function testImplodeInsteadOfJoin(): void
    {
        $this->compare(
            'token-replacement/implode-and-join',
            'token-replacement/implode-instead-of-join-post',
            (new Psr12())->addFormatter(service(UseImplodeInsteadOfJoin::class)),
            'implode() instead of join()'
        );
    }
}
