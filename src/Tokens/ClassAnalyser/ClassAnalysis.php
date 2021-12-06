<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

class ClassAnalysis
{
    public string $namespace = '';
    public string $name;
    public string $fqn;

    public ClassReference $extends;

    /** @var ClassReference[] */
    public array $implements = [];

    public bool $isClass = false;
    public bool $isInterface = false;
    public bool $isTrait = false;
    public bool $isAbstract = false;
    public bool $isFinal = false;

    /** @var ClassReference[] */
    public array $uses = [];

    /** @var ClassReference[] */
    public array $imports = [];

    public function resolveImport(string $reference): ?string
    {
        foreach ($this->imports as $import) {
            if ($import->reference === $reference) {
                return $import->fqn;
            }
        }

        return null;
    }

    public function addUses(array $uses): void
    {
        $this->uses = array_merge($this->uses, $uses);
    }
}
