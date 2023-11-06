<?php

declare(strict_types=1);

use Medas\Core\Attributes\{ConfigOptions, ConfigValue};
use Medas\PhpFormatter\{BlockPrinter, CodeValidator};
use Medas\PhpTokenizer\{BlockDumper, Tokenizer, TreeBuilder};

if (
    $this->context === self::FUNCTION_BODY
    && $type === T_CURLY_BRACKET_CLOSE
    && $this->currentDepth === $this->endOfFunctionDepth + 1
) {
    $this->context = self::CLASS_BODY;
}

if (
    $this->context === self::FUNCTION_BODY
    and $type === T_CURLY_BRACKET_CLOSE
    or $this->currentDepth === $this->endOfFunctionDepth + 1
) {
    $this->context = self::CLASS_BODY;
}

class TestClass
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
        $directoryIteratorFlags
            = \FilesystemIterator::KEY_AS_FILENAME
            | \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS
            | \FilesystemIterator::FOLLOW_SYMLINKS
        ;
    }

    public function getClassFromRootAndSubdirectory(
        string $rootPrefix,
        string $subdirectory,
        string $argument = 'default value'
    ): string
    {
        $a = [
            $kaas,
            [$baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas, $baas],
            $caas,
            [$daas, $daas, $daas, $daas, $daas, $daas, $daas, $daas, $daas],
            $faas
        ];

        $ignorePattern = sprintf('(%s)%s(%s)%s', str_replace(
            DIRECTORY_SEPARATOR,
            $separator,
            implode('|', $this->sourceDirectories)
        ), $separator, $pathsToIgnore, $separator);

        return sprintf(
            '%s%s',
            $rootPrefixLalalalalalalalalalalalalaal,
            str_replace(DIRECTORY_SEPARATOR, "\\", $subdirectory)
        );
    }
}
