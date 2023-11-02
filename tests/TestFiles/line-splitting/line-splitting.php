<?php

declare(strict_types=1);

use Medas\PhpFormatter\{BlockPrinter, CodeValidator};
use Medas\PhpTokenizer\{BlockDumper, Tokenizer, TreeBuilder};

class Test
{
    public function __construct(
        private readonly BlockDumper   $blockDumper,
        private readonly BlockPrinter  $blockPrinter,
        private readonly CodeValidator $codeValidator,
        private Tokenizer              $tokenizer,
        readonly TreeBuilder           $treeBuilder,
    )
    {
        $a = ['a', 'b', 'c'];
        $b = [
            'a',
            'b',
            'c',
        ];
    }
}
