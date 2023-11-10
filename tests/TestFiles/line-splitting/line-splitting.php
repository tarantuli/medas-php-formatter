<?php

declare(strict_types=1);

use Medas\PhpFormatter\{BlockPrinter, CodeValidator};
use Medas\PhpTokenizer\{BlockDumper, Tokenizer, TreeBuilder};

class Test
{
    private const ALLOWED_BLOCK_STARTERS = [
        T_COLON,
        T_DO,
        T_ELSE,
        T_ROUND_BRACKET_CLOSE,
        T_STRING,
        T_ARRAY,
        T_TRY,
    ];

    private const ASSIGNMENT_OPERATORS = [
        T_AND_EQUAL,
        T_ASSIGNMENT,
        T_CONCAT_EQUAL,
        T_DIV_EQUAL,
        T_MINUS_EQUAL,
        T_MOD_EQUAL,
        T_MUL_EQUAL,
        T_OR_EQUAL,
        T_PLUS_EQUAL,
        T_SL_EQUAL,
        T_SR_EQUAL,
        T_XOR_EQUAL,
    ];

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

    public function getRestProperties(): array
    {
        return array_merge(self::getRestProperties(), [
            'name' => [
                'getter' => 'getName',
                'setter' => 'setName',
            ],
        ]);
    }
}
