<?php

namespace MyNamespace;

use AnotherNamespace\ClassName;
use So\Many\Layers\Namespace as AliasedNamespace;
use YetAnotherNamespace\ClassName as AliasOfClassName;

class MyClass extends ChildNamespace\BaseClass implements Interfaces\MyInterface, \Absolute\Path\To\AnotherInterface
{
    use AnotherNamespace\TraitName;

    public function a(Relative\ClassName $relativeClass): Relative\ReturnType
    {
        if ($relativeClass instanceof AliasedNamespace\SemiRelativeClass) {
            return false;
        }

        return ClassName::class;
    }

    public function b(\Absolute\ClassName $absoluteClass): \Absolute\ReturnType
    {
        return [new AliasOfClassName, new NewWithParentheses()];
    }

    public function c(ClassA|ClassB $aClass): ClassC|ClassD|null
    {
        return null;
    }
}
