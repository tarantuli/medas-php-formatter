<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatter\Settings\MinimalSize;
use Medas\PhpFormatter\Settings\Psr12;

class FormatterTest extends BaseTestClass
{
    public function testEmptyMethodBody(): void
    {
        $this->assertRemainsTheSame('empty-method-body', new Medas());
    }

    public function testPsr12IfElse(): void
    {
        $this->assertRemainsTheSame('psr12-ifelse', new Psr12());
    }

    public function testPlusAndMinus(): void
    {
        $this->assertRemainsTheSame('plus-and-minus', new Medas());
    }

    public function testTernaryExpressions(): void
    {
        $this->assertRemainsTheSame('ternary-expressions', new Medas());
    }

    public function testMethodsAndFunctions(): void
    {
        $this->assertRemainsTheSame('methods-and-functions', new Medas());
    }

    public function testParentheses(): void
    {
        $this->assertRemainsTheSame('parentheses', new Medas());
    }

    public function testSquareBrackets(): void
    {
        $this->assertRemainsTheSame('square-brackets', new Medas());
    }

    public function testVisibility(): void

    {
        $this->assertChanges('psr12-visibility-pre', 'psr12-visibility-post', new Psr12());
    }

    public function testMatch(): void
    {
        $this->assertRemainsTheSame('match', new Medas());
    }

    public function testSwitch(): void
    {
        $this->assertRemainsTheSame('switch-statement', new Psr12());
    }

    public function testComments(): void
    {
        $this->assertRemainsTheSame('comments', new Medas());
    }

    public function testBasicPreformatted(): void
    {
        $this->assertRemainsTheSame('basic-preformatted', new Medas());
    }

    public function testRequiredWhitespace(): void
    {
        $this->assertChanges('basic-preformatted', 'basic-preformatted-minimal-whitespace', new MinimalSize());
    }
}
