<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\TestFiles\reordering;

abstract class AbstractClass
{
    abstract public function abstractPublicFunction();

    abstract public static function abstractPublicStaticFunction();

    abstract protected function abstractProtectedFunction();

    abstract protected static function abstractStaticFunction();

    public function __destruct()
    {
    }

    public function __construct()
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return $this->privateProperty;
    }

    protected const PROTECTED_CONST = 1;

    public function __unserialize(array $data): void
    {
    }

    protected static function protectedStaticFunction(): void
    {
    }

    public static function publicStaticFunction(): void
    {
    }

    private static function privateStaticFunction(): void
    {
    }

    private string $privateProperty;

    public function publicFunction(): void
    {
    }

    protected function protectedFunction(): void
    {
    }

    private function privateFunction(): void
    {
    }

    /**
     * This is a public function that should not be sorted alphabetically, it should stay above entrypointA()
     */
    public function entrypointB(): void
    {
        $this->calledByB();
        $this->alsoCalledByB();
    }

    public function entrypointA(): void
    {
        $this->calledByA();
    }

    private function calledByA(): void
    {
    }

    private function alsoCalledByB(): void
    {
    }

    private function calledByB(): void
    {
    }

    private const PRIVATE_CONST = 1;
    public const PUBLIC_CONST = 1;

    public string $publicProperty;
    protected string $protectedProperty;
}
