<?php

/**
 * Formats every PHP file in the given directories and checks two things:
 *
 * - The code means the same after formatting: the syntax trees before and after (from nikic/php-parser) are equal,
 *   apart from what the formatter changes on purpose. Names are resolved, imports, comments and positions left out,
 *   ?T is read as T|null, members without visibility as public, and the order of class members and of constructor
 *   parameters is ignored (they are reordered and sorted).
 * - Formatting is stable: formatting the result again changes nothing.
 *
 * The formatted files are written to the output directory, so the results of two runs can be compared with
 * "git diff --no-index <first output> <second output>". The output directory gets a file .check-corpus-output, and
 * directories with that file are skipped, so the output of earlier runs is never checked itself.
 *
 * Usage: php bin/check-corpus.php [--settings=Medas|Psr12|MinimalSize] <output directory> <source directory>...
 */

declare(strict_types=1);

use Medas\PhpFormatter\{Formatter, Settings};
use PhpParser\{Node, NodeDumper, NodeTraverser, NodeVisitor, NodeVisitorAbstract, ParserFactory};

const OUTPUT_MARKER = '.check-corpus-output';

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../phpunit.bootstrap.php';

$arguments = array_slice($argv, 1);
$settingsName = 'Medas';

foreach ($arguments as $index => $argument) {
    if (str_starts_with($argument, '--settings=')) {
        $settingsName = substr($argument, strlen('--settings='));
        unset($arguments[$index]);
    }
}

$arguments = array_values($arguments);

if (count($arguments) < 2) {
    fwrite(STDERR, "Usage: php bin/check-corpus.php [--settings=Medas] <output directory> <source directory>...\n");

    exit(2);
}

$outputDirectory = rtrim(array_shift($arguments), '/\\');

// The marker makes later runs skip this output, also when it is inside a source directory
writeFile($outputDirectory . '/' . OUTPUT_MARKER, "Output of bin/check-corpus.php\n");
$settingsClass = 'Medas\\PhpFormatter\\Settings\\' . $settingsName;
$settings = new $settingsClass();
$formatter = service(Formatter::class);
$parser = (new ParserFactory())->createForHostVersion();

$counts = ['files' => 0, 'unchanged' => 0, 'changed' => 0];
$problems = ['crash' => [], 'meaning' => [], 'unstable' => []];

foreach ($arguments as $sourceDirectory) {
    $sourceDirectory = rtrim($sourceDirectory, '/\\');

    foreach (phpFiles($sourceDirectory) as $file) {
        $relative = basename($sourceDirectory) . '/' . substr($file, strlen($sourceDirectory) + 1);
        $code = file_get_contents($file);
        ++$counts['files'];

        try {
            $formatted = $formatter->format($code, $settings);
        }
        catch (Throwable $exception) {
            $problems['crash'][] = sprintf('%s: %s: %s', $relative, $exception::class, $exception->getMessage());

            continue;
        }

        writeFile($outputDirectory . '/' . $relative, $formatted);
        ++$counts[$formatted === $code ? 'unchanged' : 'changed'];

        try {
            if (normalizedTree($parser, $code) !== normalizedTree($parser, $formatted)) {
                $problems['meaning'][] = $relative;
            }
        }
        catch (Throwable $exception) {
            $problems['meaning'][] = sprintf('%s (%s)', $relative, $exception->getMessage());
        }

        try {
            if ($formatter->format($formatted, $settings) !== $formatted) {
                $problems['unstable'][] = $relative;
            }
        }
        catch (Throwable $exception) {
            $problems['unstable'][] = sprintf('%s (%s: %s)', $relative, $exception::class, $exception->getMessage());
        }
    }
}

printf(
    "%d files: %d unchanged, %d changed by formatting. Output in %s\n",
    $counts['files'],
    $counts['unchanged'],
    $counts['changed'],
    $outputDirectory
);

$titles = [
    'crash' => 'The formatter crashed',
    'meaning' => 'The formatted code means something else',
    'unstable' => 'Formatting the result again changes it',
];

foreach ($problems as $kind => $files) {
    printf("\n%s: %d\n", $titles[$kind], count($files));

    foreach ($files as $file) {
        echo "    $file\n";
    }
}

exit(array_merge(...array_values($problems)) === [] ? 0 : 1);

/**
 * @return Generator<string>
 */
function phpFiles(string $directory): Generator
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            fn(SplFileInfo $file) => !in_array($file->getFilename(), ['vendor', 'node_modules', '.git', '.idea'], true)
                && !($file->isDir() && is_file($file->getPathname() . '/' . OUTPUT_MARKER))
        )
    );

    $files = [];

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = str_replace('\\', '/', $file->getPathname());
        }
    }

    sort($files);

    yield from $files;
}

function writeFile(string $path, string $content): void
{
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    file_put_contents($path, $content);
}

/**
 * The syntax tree as text, without what formatting may change on purpose: imports (names are resolved to their full
 * name instead), comments, positions, and ?T versus T|null.
 */
function normalizedTree(PhpParser\Parser $parser, string $code): string
{
    $traverser = new NodeTraverser();
    $traverser->addVisitor(new PhpParser\NodeVisitor\NameResolver());
    $traverser->addVisitor(new class extends NodeVisitorAbstract {
        public function leaveNode(Node $node): int|Node|null
        {
            if ($node instanceof Node\Stmt\Use_ || $node instanceof Node\Stmt\GroupUse) {
                return NodeVisitor::REMOVE_NODE;
            }

            if ($node instanceof Node\NullableType) {
                return new Node\UnionType([$node->type, new Node\Identifier('null')]);
            }

            $node->setAttributes([]);

            if ($node instanceof Node\Stmt\ClassMethod
                    || $node instanceof Node\Stmt\Property
                    || $node instanceof Node\Stmt\ClassConst) {
                if (($node->flags & PhpParser\Modifiers::VISIBILITY_MASK) === 0) {
                    $node->flags |= PhpParser\Modifiers::PUBLIC;
                }
            }

            if ($node instanceof Node\Stmt\ClassMethod && $node->name->toLowerString() === '__construct') {
                usort($node->params, fn($a, $b) => dumpNode($a) <=> dumpNode($b));
            }

            if ($node instanceof Node\Stmt\ClassLike) {
                usort($node->stmts, fn($a, $b) => dumpNode($a) <=> dumpNode($b));
            }

            return null;
        }
    });

    return dumpNode($traverser->traverse($parser->parse($code) ?? []));
}

function dumpNode(Node|array $node): string
{
    return (new NodeDumper(['dumpComments' => false, 'dumpPositions' => false]))->dump($node);
}
