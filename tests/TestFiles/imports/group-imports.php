<?php

namespace MyNamespace;

use Medas\Bore\UngroupedReference;
use Medas\Console\Formats\Format;
use Medas\Core\Attributes\{EventListener as EL, SubLevel\Service};
use Medas\Dore\Class as AliasedClass;
use Medas\PhpTokenizer\{BlockDumper, TokenCollection, Tokenizer, TokenTree, TreeBuilder};

#[Service]
class TestClass extends UngroupedReference implements AliasedClass
{
    #[EL, BlockDumper, TokenCollection, Tokenizer, TokenTree, TreeBuilder]
    public function test(): void
    {
    }

    /** @param Format|Format[] $formats */
    private function format(string $string, mixed $formats): void
    {
    }
}
