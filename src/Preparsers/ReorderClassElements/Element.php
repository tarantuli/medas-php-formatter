<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

class Element
{
    public string $text = '';
    public string|null $name = null;
    public string $sortingKey;

    // Properties
    public int $isUse = Properties::IS_NOT_USE_STATEMENT;
    public int $isAbstract = Properties::IS_NOT_ABSTRACT;
    public int $isConst = Properties::IS_NOT_CONST;
    public int $isStatic = Properties::IS_NOT_STATIC;
    public int $isMethod = Properties::IS_NOT_METHOD;
    public int $isMagicMethod = Properties::IS_NOT_MAGIC_METHOD;
    public int $accessModifier = Properties::IS_PUBLIC;

    // Method references
    public array $methodReferences = [];
}
