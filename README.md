# medas-php-formatter

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A PHP source code formatter built on `medas-php-tokenizer`. It tokenizes PHP source code, builds a token tree, passes it through a configurable pipeline of formatters, and serializes the result back to a formatted string.

**Pipeline stages:**

| Stage      | Classes                  | Purpose                                                                 |
|------------|--------------------------|-------------------------------------------------------------------------|
| Preparsers | `Preparser` implementors | Text-level transforms before tokenization (e.g. `ReorderClassElements`) |
| Formatters | `Formatter` implementors | Tree-level transforms — the main formatting pass                        |

**Two built-in settings profiles:**

`Settings\Psr12` — PSR-12 compliant formatting:
- Curly block depth tracking, visibility markers, whitespace normalisation, keyword lowercasing
- Blank line rules, `else`/`catch`/`while` placement, `switch` indentation
- Single-line control bodies enclosed in braces, PHP 8.4 property hook formatting
- 80-character max line length

`Settings\Medas` — extends `Psr12` with additional Medas-specific rules:
- Named argument alignment (`AlignArgumentNames`)
- Import grouping and normalization (`NormalizeImports`)
- Long-line splitting with configurable breakpoint sets
- Soft-line splitting for function chains
- Trailing comma insertion
- Class element reordering (preparer stage)
- `#[Service]` constructor parameter alphabetic sorting
- `die` → `exit`, `?T` → `T|null` replacements
- Blank line rules between class sections, statement groups, and object creation/manipulation

## Configuration options

| Option                                        | Default | Description                                             |
|-----------------------------------------------|---------|---------------------------------------------------------|
| `php-formatter.validate-source-code`          | `true`  | Validate source PHP syntax before formatting            |
| `php-formatter.validate-reformatted-code`     | `true`  | Validate reformatted output PHP syntax                  |
| `php-formatter.path-to-php`                   | none    | Path to the PHP executable used for syntax validation   |
| `php-formatter.max-line-length`               | `120`   | Hard maximum line length for `LongLineSplitter`         |
| `php-formatter.soft-max-line-length`          | `100`   | Soft maximum for `SoftLineSplitter`                     |
| `php-formatter.min-import-group-prefix-depth` | `2`     | Minimum namespace depth for `NormalizeImports` grouping |
| `php-formatter.max-import-group-child-depth`  | `1`     | Maximum nesting depth for import groups                 |
| `php-formatter.dump-parsed-tree`              | `false` | Print the token tree after parsing (debugging)          |
| `php-formatter.dump-result-tree`              | `false` | Print the token tree after formatting (debugging)       |
| `php-formatter.dump-option-assessment`        | `false` | Print line-split option assessments (debugging)         |

## Usage

### Package developer context

Register the package and inject `Formatter`:

```php
use Medas\PhpFormatter\PhpFormatterPackage;

PhpFormatterPackage::instance();
```

**Formatting with the Medas profile:**

```php
use Medas\PhpFormatter\{Formatter, Settings\Medas};
use Medas\Core\Attributes\Service;

#[Service]
readonly class PhpFileFormatter
{
    public function __construct(
        private Formatter $formatter,
    ) {}

    public function format(string $phpSource): string
    {
        return $this->formatter->format($phpSource, new Medas());
    }
}
```

**Formatting with the PSR-12 profile:**

```php
use Medas\PhpFormatter\Settings\Psr12;

$formatted = $this->formatter->format($phpSource, new Psr12());
```

**Formatting with a custom extension of the Medas profile:**

```php
use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatter\Formatters\Replacements\UseImplodeInsteadOfJoin;

// Add extra formatters on top of the Medas defaults
$formatted = $this->formatter->format($phpSource, new Medas(
    additionalFormatters: [UseImplodeInsteadOfJoin::class],
));
```

**Formatting a file on disk:**

```php
$source    = file_get_contents($path);
$formatted = $this->formatter->format($source, new Medas());

file_put_contents($path, $formatted);
```

**Writing a custom formatter:**

```php
use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\Token;

#[Service]
readonly class RemoveFinalKeyword extends BaseFormatter
{
    // Formatters with a higher priority run earlier; this one runs before the whitespace formatters
    public function priority(): int
    {
        return 2000;
    }

    public function format(Job $job): void
    {
        // Collected first, as removing tokens while walking the tree would skip some
        $finals = array_filter(iterator_to_array($job->tree, false), fn(Token $token) => $token->is(T_FINAL));

        foreach ($finals as $token) {
            $token->statement->removeToken($token);
        }
    }
}

// Pass it in via additionalFormatters:
$formatted = $this->formatter->format($source, new Medas(
    additionalFormatters: [RemoveFinalKeyword::class],
));
```

Custom formatters are resolved via the service container — they can declare dependencies in their constructors as normal.

**Writing a custom preparser:**

```php
use Medas\PhpFormatter\{Job, Preparsers\Preparser};
use Medas\Core\Attributes\Service;

#[Service]
readonly class StripAnnotations implements Preparser
{
    public function preparse(Job $job): void
    {
        // Operates on $job->code (string) before tokenization
        $job->code = preg_replace('/@deprecated[^\n]*/', '', $job->code);
    }
}
```

### Backend user context

**Syntax validation** — by default, both `validate-source-code` and `validate-reformatted-code` are enabled. They run `php -l` via the configured `path-to-php`. Set `path-to-php` to the absolute path of your PHP binary if the formatter is used in an environment where `php` is not on `$PATH`:

```yaml
php-formatter:
  path-to-php: /usr/bin/php8.4
  validate-source-code: true
  validate-reformatted-code: true
```

`SourceCodeIsInvalid` is thrown before formatting if the source fails `php -l`. `ReformattedCodeIsInvalid` is thrown after formatting if the output fails `php -l` — this indicates a formatter bug and should be reported.

**Line length settings** — `max-line-length` controls `LongLineSplitter` (hard wrapping) and `soft-max-line-length` controls `SoftLineSplitter` (wraps only at natural split points). Set both in YAML or leave them at the defaults:

```yaml
php-formatter:
  max-line-length: 120
  soft-max-line-length: 100
```

**Debugging a formatter** — enable the tree dumps to inspect what the token tree looks like before and after formatting:

```yaml
php-formatter:
  dump-parsed-tree: true
  dump-result-tree: true
```

This outputs the tree to stdout via `medas-console`. Disable in production.

## Checking the formatter on a large code base

`bin/check-corpus.php` formats every PHP file in one or more directories and reports files where the formatter
crashes, where the formatted code means something else, and where formatting the result again changes it. Meaning is
compared on the syntax tree from `nikic/php-parser`, ignoring what the formatter changes on purpose: imports, comments,
`?T` versus `T|null`, implicit `public`, and the order of class members and constructor parameters.

```bash
php bin/check-corpus.php [--settings=Medas|Psr12|MinimalSize] <output directory> <source directory>...
```

The formatted files are written to the output directory. Run it before and after a change and compare the two outputs
with `git diff --no-index <before> <after>` to see every file the change affects. The output directory gets a file `.check-corpus-output`,
and directories with that file are skipped, so an output directory inside a source directory is never checked itself.

## Upgrading from 3.x

4.0 needs `medas-php-tokenizer` 4.1 and `medas-php-class-analysis` 3.2, and reads everything about the code from the data the tokenizer now provides.

**Removed:**

| Removed                                                    | Instead                                                                                      |
|------------------------------------------------------------|----------------------------------------------------------------------------------------------|
| The preformatter stage: `Preformatters\Preformatter`       | A formatter with a high `priority()`; the original layout is in `originalLineBreaksAfter`    |
| `Preformatters\NoCommentsAtLineEnd`                        | `Formatters\Replacements\NoCommentsAtLineEnd`, now an ordinary formatter                     |
| `Formatters\Replacements\MoveCommentsAtLineEnd`            | Merged into `NoCommentsAtLineEnd`                                                            |
| `$additionalPreformatters` of `Settings`, `Psr12`, `Medas` | Pass a formatter in `$additionalFormatters`. Positional arguments after it move up one place |
| `Settings::$preformatters`                                 | Nothing                                                                                      |
| `Formatters\Helpers\ReturnTypeTokens`                      | `Token::$typeDeclaration === TypeDeclarationKind::ReturnType`, `TokenRole::ReturnTypeColon`  |

**Changed:** `Formatters\Tokens` holds token ids (`TokenType::…`) instead of the old text constants. A custom formatter that uses the tokenizer's `T_COMMA`-style constants, `Contexts` or `inTypeDeclaration` has to move to the tokenizer 4.1 data; its README has a table of replacements.

**Formatted output** changes in a few places, all of them fixes. Format a project once after upgrading, and review the diff, for example with `bin/check-corpus.php`:

- The `{` of an anonymous class stays on the line of `new class`, and there is a space between `)` and a keyword, as in `new class(1) extends Base {`.
- A comment at the end of a line that only closes brackets, like `}, // Comment`, stays after it, on a line of its own. A comment on a line of its own inside a statement keeps that line.
- A `match` that is an argument of a split call is indented along with it, like a closure, and has no blank lines between its arms.
- A statement that is split before `->` keeps its blank line after its last part, not after its first.
- `use A, B { … }`, `namespace App { … }`, `Foo::{$m}()`, DNF types and alternative syntax are formatted correctly.
