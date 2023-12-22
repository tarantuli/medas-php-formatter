<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\TestFiles\reordering;

abstract class AbstractClass
{
    abstract public static function abstractPublicStaticFunction();

    abstract protected static function abstractStaticFunction();

    abstract public function abstractPublicFunction();

    abstract protected function abstractProtectedFunction();

    public const PUBLIC_CONST = 1;
    protected const PROTECTED_CONST = 1;
    private const PRIVATE_CONST = 1;

    public static function publicStaticFunction(): void
    {
    }

    protected static function protectedStaticFunction(): void
    {
    }

    private static function privateStaticFunction(): void
    {
    }

    public string $publicProperty;
    protected string $protectedProperty;
    private string $privateProperty;

    public function __construct()
    {
    }

    public function __destruct()
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

    public function __unserialize(array $data): void
    {
    }

    public function publicFunction(): void
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

    private function calledByB(): void
    {
    }

    private function alsoCalledByB(): void
    {
    }

    public function entrypointA(): void
    {
        $this->calledByA();
    }

    private function calledByA(): void
    {
    }

    protected function protectedFunction(): void
    {
    }

    private function privateFunction(): void
    {
    }
}
