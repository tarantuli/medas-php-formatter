# medas-php-formatter

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A PHP source code formatter built on `medas-php-tokenizer`. It tokenizes PHP source code, builds a token tree, passes it through a configurable pipeline of formatters, and serializes the result back to a formatted string.

**Pipeline stages:**

| Stage         | Classes                     | Purpose                                                                  |
|---------------|-----------------------------|--------------------------------------------------------------------------|
| Preparsers    | `Preparser` implementors    | Text-level transforms before tokenization (e.g. `ReorderClassElements`)  |
| Preformatters | `Preformatter` implementors | Token-level transforms before tree building (e.g. `NoCommentsAtLineEnd`) |
| Formatters    | `Formatter` implementors    | Tree-level transforms — the main formatting pass                         |

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
use Medas\PhpFormatter\{Formatters\Formatter as FormatterInterface, Job};
use Medas\Core\Attributes\Service;

#[Service]
readonly class RemoveFinalKeyword implements FormatterInterface
{
    public function format(Job $job): void
    {
        // Walk the token tree and remove T_FINAL tokens
        foreach ($job->tree->block()->tokens() as $token) {
            if ($token->is(\T_FINAL)) {
                $token->remove();
            }
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
