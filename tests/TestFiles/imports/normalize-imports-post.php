<?php

namespace MyNamespace;

use GlobalNamespaceClass;
use AnotherNamespace\ClassName;
use So\Many\Layers\Namespace as AliasedNamespace;
use YetAnotherNamespace\ClassName as AliasOfClassName;

class MyClass extends ChildNamespace\BaseClass implements Interfaces\MyInterface, \Absolute\Path\To\AnotherInterface
{
    use AnotherNamespace\TraitName;
    use Comma, Separated, Traits;

    public function a(Relative\ClassName $relativeClass): Relative\ReturnType
    {
        if ($relativeClass instanceof AliasedNamespace\SemiRelativeClass) {
            return false;
        }

        return AlsoRelative\ClassName::class;
    }

    public function b(\Absolute\ClassName $absoluteClass): \Absolute\ReturnType
    {
        return [new AliasOfClassName, new NewWithParentheses()];
    }

    public function c(ClassA|ClassB $aClass): ClassC|ClassD|null
    {
        return \Absolute\Classname::class;
    }

    public function d(StringClass $stringClass): StringClass
    {
        return StringClass::class;
    }

    public function e(int $int, string $string, array $array, object $object, bool $bool): int|string|array|object|bool|null
    {
        return null;
    }
}
