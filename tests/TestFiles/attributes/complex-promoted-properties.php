<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpTokenizer\{BlockDumper, Tokenizer, TreeBuilder};

#[Service]
readonly class Formatterx
{
    public function __construct(
        private BlockDumper   $blockDumper,
        private BlockPrinter  $blockPrinter,
        private CodeValidator $codeValidator,
        private Tokenizer     $tokenizer,
        private TreeBuilder   $treeBuilder,

        #[ConfigValue(ConfigOptions\DumpParsedTree::class)]
        private bool          $dumpParseTree,

        #[ConfigValue(ConfigOptions\DumpResultTree::class)]
        private bool          $dumpResultTree,
    )
    {
    }
}
