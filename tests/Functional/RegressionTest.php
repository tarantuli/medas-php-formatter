<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatterTest\Functional\Replacements\UseImplodeInsteadOfJoinSettings;

/**
 * Regression tests for the bugs found in the analysis of php-tokenizer, php-class-analysis and php-formatter
 * (November 2026). Each test describes the correct result; they all fail until the bug is fixed.
 *
 * Once a bug is fixed, the test can move to the test class of its area.
 */
class RegressionTest extends BaseTestClass
{
    // ---------------------------------------------------------------------------------------------------------------
    // Output is invalid PHP or behaves differently
    // ---------------------------------------------------------------------------------------------------------------

    /**
     * `ServiceConstructionParameters` moves the last parameter, which has no trailing comma, between the others.
     */
    public function testSortingSingleLineConstructor(): void
    {
        $this->assertChanges('sorting/single-line-constructor-pre', 'sorting/single-line-constructor-post');
    }

    /**
     * `SingleLineControlBodiesEncloser` waits for a `)` before the body, which `else` doesn't have.
     */
    public function testElseWithSingleLineBody(): void
    {
        $this->assertChanges(
            'token-replacement/else-single-line-body-pre',
            'token-replacement/else-single-line-body-post'
        );
    }

    /**
     * `while (…) foo();` is mistaken for the end of a do-while, so it isn't enclosed and is merged onto the `}`.
     */
    public function testWhileWithSingleLineBody(): void
    {
        $this->assertChanges(
            'token-replacement/while-single-line-body-pre',
            'token-replacement/while-single-line-body-post'
        );
    }

    /**
     * The ternary `?` in a default value is marked `inTypeDeclaration`, so `UseUnionNullInsteadOfNullable` rewrites it.
     */
    public function testTernaryInDefaultValueIsNotNullable(): void
    {
        $this->assertRemainsTheSame('token-replacement/union-null-ternary-default');
    }

    /**
     * `UseImplodeInsteadOfJoin` renames method calls and skips `join()` in statements that aren't generic.
     */
    public function testImplodeInsteadOfJoinOnlyReplacesTheFunction(): void
    {
        $this->compare(
            'token-replacement/implode-method-call-pre',
            'token-replacement/implode-method-call-post',
            new UseImplodeInsteadOfJoinSettings(),
            'only the global join() function is replaced'
        );
    }

    /**
     * `ImportsFinder` only reads the first name of `use A, B;`, so `NormalizeImports` drops the second import.
     */
    public function testMultipleImportsPerStatement(): void
    {
        $this->assertChanges(
            'imports/multiple-imports-per-statement-pre',
            'imports/multiple-imports-per-statement-post'
        );
    }

    /**
     * `DoccommentParser` doesn't read `@see`, so an import that's only used there is removed.
     */
    public function testImportOnlyUsedInSeeTag(): void
    {
        $this->assertRemainsTheSame('imports/see-tag-import');
    }

    /**
     * `NewImportsInserter` deletes the import statements including the comments in front of them.
     */
    public function testCommentAboveImports(): void
    {
        $this->assertRemainsTheSame('imports/comment-above-imports');
    }

    /**
     * `MoveCommentsAtLineEnd` moves a comment inside a multi-line statement to before the previous statement, which
     * here is `<?php`, turning it into inline HTML.
     */
    public function testCommentInMultiLineArray(): void
    {
        $this->assertChanges(
            'token-replacement/comment-in-multiline-array-pre',
            'token-replacement/comment-in-multiline-array-post'
        );
    }

    /**
     * The class analysis assumes one namespace per file, so the imports of the second namespace were moved to the
     * first. Files with more than one namespace keep their imports.
     */
    public function testMultipleNamespacesKeepTheirImports(): void
    {
        $this->assertRemainsTheSame('imports/multiple-namespaces');
    }

    /**
     * After a class with an empty body, `ContextAdder` kept the class body context, so a function in the next braced
     * namespace was taken for a method and got `public`.
     */
    public function testFunctionInBracedNamespace(): void
    {
        $this->assertRemainsTheSame('bugs/function-in-braced-namespace');
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Crashes
    // ---------------------------------------------------------------------------------------------------------------

    /**
     * The `;` inside the adaptation block ends `inUseStatement`, so the closing `}` pops a block that was never opened.
     */
    public function testTraitAdaptationBlock(): void
    {
        $this->assertRemainsTheSame('bugs/trait-adaptation');
    }

    /**
     * `Block::getIndex()` returns `null` for a statement in a child block, which `array_splice()` doesn't accept.
     */
    public function testBracedNamespace(): void
    {
        $this->assertRemainsTheSame('imports/braced-namespace');
    }

    /**
     * The constant `STATUS` is taken for a class reference and throws `ImportLabelCaseMismatch` against `Status`.
     */
    public function testConstantNamedLikeImport(): void
    {
        $this->assertRemainsTheSame('imports/constant-named-like-import');
    }

    /**
     * `PhpToken::is()` compares a kind given as text with the token's text, so the string part `]` of `"[$a]"` was a
     * closing bracket, and the tokenizer found the brackets unbalanced.
     */
    public function testBracketsInStrings(): void
    {
        $this->assertRemainsTheSame('bugs/brackets-in-strings');
    }

    /**
     * `Psr12ElseifWhileCatch` merged an `else:` of alternative syntax into the block before it, which has no tokens.
     */
    public function testAlternativeSyntaxElse(): void
    {
        $this->assertRemainsTheSame('bugs/alternative-syntax-else');
    }

    /**
     * `MoveCommentsAtLineEnd` moved a comment that was a statement of its own, and left the empty statement behind.
     */
    public function testCommentAloneAfterCase(): void
    {
        $this->assertChanges('bugs/comment-alone-after-case-pre', 'bugs/comment-alone-after-case-post');
    }

    // ---------------------------------------------------------------------------------------------------------------
    // Wrong layout
    // ---------------------------------------------------------------------------------------------------------------

    /**
     * A `}` followed by `(` isn't seen as a block closer, so everything after it is nested one level too deep.
     */
    public function testBlockCloserFollowedByParenthesis(): void
    {
        $this->assertRemainsTheSame('bugs/block-closer-followed-by-parenthesis');
    }

    /**
     * The `{` in `Foo::{$method}()` opens a block that is never closed.
     */
    public function testDynamicStaticMethodName(): void
    {
        $this->assertRemainsTheSame('bugs/dynamic-static-method-name');
    }

    /**
     * The `)` of `(A&B)` ends the argument list for the type declaration checks, and `&` isn't a type token.
     */
    public function testDnfTypes(): void
    {
        $this->assertRemainsTheSame('bugs/dnf-types');
    }

    /**
     * The tokenizer ended a switch case at the first colon, so the colon of a ternary in a switch split the
     * statement in two.
     */
    public function testTernaryInSwitch(): void
    {
        $this->assertRemainsTheSame('bugs/ternary-in-switch');
    }

    /**
     * Neither `Psr12ElseifWhileCatch` nor `MedasElseifWhileCatch` handles `finally`.
     */
    public function testTryCatchFinally(): void
    {
        $this->assertRemainsTheSame('bugs/try-catch-finally');
    }

    /**
     * `Psr12VisibilityMarkers` prepends `public` to the statement, which starts with the docblock.
     */
    public function testVisibilityAfterDocblock(): void
    {
        $this->assertChanges('psr12/visibility-after-docblock-pre', 'psr12/visibility-after-docblock-post');
    }

    /**
     * `KeepOriginalObjectOperatorBreaks` only checks `->`, not `?->`.
     */
    public function testKeepOriginalNullsafeOperatorBreaks(): void
    {
        $this->assertRemainsTheSame('line-splitting/keep-original-nullsafe-operator-breaks');
    }

    /**
     * Doc comments keep their original indentation when the statement they belong to is re-indented.
     */
    public function testDocblockIndentation(): void
    {
        $this->assertChanges('bugs/docblock-indentation-pre', 'bugs/docblock-indentation-post');
    }

    /**
     * `IsServiceClassFinder` only recognised the attribute by its short name, so `#[Attributes\Service]` was no
     * service class and its constructor parameters weren't sorted.
     */
    public function testQualifiedServiceAttribute(): void
    {
        $this->assertChanges(
            'sorting/qualified-service-attribute-pre',
            'sorting/qualified-service-attribute-post'
        );
    }

    /**
     * `IsServiceClassFinder` took any `Service` inside an attribute for the attribute name, also a constant in its
     * arguments.
     */
    public function testServiceInAttributeArgument(): void
    {
        $this->assertRemainsTheSame('sorting/service-in-attribute-argument');
    }
}
