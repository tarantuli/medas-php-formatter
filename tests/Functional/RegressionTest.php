<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Psr12;
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
     * `NoCommentsAtLineEnd` moves a comment inside a multi-line statement to before the previous statement, which
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

    /**
     * `PropertyHookFormatter` took the first `}` in the class body for the end of a hook list, so with a method before
     * it, the property moved into the method. With `Psr12` settings, because `ReorderClassElements` hides it.
     */
    public function testVirtualHooksAfterMethod(): void
    {
        $this->assertRemainsTheSame('psr12/virtual-hooks-after-method', new Psr12());
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
     * `NoCommentsAtLineEnd` moved a comment that was a statement of its own, and left the empty statement behind.
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
     * `Psr12BlankLines` took an anonymous class for a class declaration and put its `{` on the next line, and
     * `SwitchIndentationAdder` only closed a switch at a `}`, so after `endswitch;` everything was indented.
     */
    public function testAnonymousClassAndAlternativeSwitch(): void
    {
        $this->assertRemainsTheSame('bugs/anonymous-class-and-alternative-switch');
    }

    /**
     * `Psr12Whitespace` put a space before the `{` of every class, also of a named class, which has it on the next
     * line. A class header that just fits then got too long, and was split before `implements`.
     */
    public function testClassHeaderAtLineLength(): void
    {
        $this->assertRemainsTheSame('bugs/class-header-at-line-length');
    }

    /**
     * `BlockTypeResolver` didn't skip a comment when it looked back from a `{`, so the body of
     * `function f(): void // Comment` was a plain block, and the `{` moved onto the line of the function.
     */
    public function testCommentBeforeFunctionBody(): void
    {
        $this->assertChanges('bugs/comment-before-function-body-pre', 'bugs/comment-before-function-body-post');
    }

    /**
     * `ElementsFinder` counted the `}` of `"${a}"` but not its `${`, so reordering the class elements broke the code.
     */
    public function testDollarBraceInterpolation(): void
    {
        $this->assertChanges('bugs/dollar-brace-interpolation-pre', 'bugs/dollar-brace-interpolation-post');
    }

    /**
     * The `{` of `"{$a}"` is `T_CURLY_OPEN`, but the `}` that closes it is a plain `}`. The text constant
     * `T_CURLY_BRACKET_OPEN` matched both kinds of `{`; `TokenType::CURLY_BRACKET_OPEN` only the plain one, so the line
     * splitter took that `}` for the end of the arguments, and split after `$tag->name`.
     */
    public function testInterpolationInSplitCall(): void
    {
        $this->assertRemainsTheSame('bugs/interpolation-in-split-call');
    }

    /**
     * `AlignArgumentNames` lined up a parameter without a type with a placeholder token of spaces, which later
     * formatters took for the first token of the statement. Now the statement has `extraSpacesBefore`.
     */
    public function testUntypedParameterAlignment(): void
    {
        $this->assertRemainsTheSame('bugs/untyped-parameter-alignment');
    }

    /**
     * A comment on a line of its own between the arguments of a call that hugs a closure ended up after the "}," of
     * the closure, and was moved somewhere else the next time. A comment at the end of the line "}," was moved between
     * the "}" and the ",".
     */
    public function testCommentsBetweenClosureArguments(): void
    {
        $this->assertChanges(
            'bugs/comments-between-closure-arguments-pre',
            'bugs/comments-between-closure-arguments-post'
        );
    }

    /**
     * `CurlyBlockDepthPropagator` didn't take the arms of a match along when the call it is an argument of was split.
     */
    public function testMatchInSplitCall(): void
    {
        $this->assertChanges('bugs/match-in-split-call-pre', 'bugs/match-in-split-call-post');
    }

    /**
     * `NoCommentsAtLineEnd` found the start of a line by line numbers. Now it uses the original layout, and hands the
     * line break after a moved comment to the token before it, so `KeepOriginalObjectOperatorBreaks` still sees the
     * line break before `->where()`. That formatter left the blank line of a statement it split after its first part.
     */
    public function testCommentBeforeObjectOperator(): void
    {
        $this->assertChanges('bugs/comment-before-object-operator-pre', 'bugs/comment-before-object-operator-post');
    }

    /**
     * `Psr12Whitespace` put no space between a closing bracket and a keyword, as in `new class(1)extends Base`. PHP
     * doesn't need it, so `RequiredWhitespace` doesn't add it either.
     */
    public function testAnonymousClassArguments(): void
    {
        $this->assertRemainsTheSame('bugs/anonymous-class-arguments');
    }

    /**
     * `StatementSplitter` counted brackets from the end of the region, so after splitting off the `use (` list of a
     * closure it no longer split the list itself. And `BlankLineBetweenObjectCreationAndManipulation` crashed on
     * `$entity->...` as the first statement in a closure that `$entity = ...` opens.
     */
    public function testClosureUseList(): void
    {
        $this->assertChanges('line-splitting/closure-use-list-pre', 'line-splitting/closure-use-list-post');
    }

    /**
     * `UseUnionNullInsteadOfNullable` and `Psr12Whitespace` only knew parameter, return and property types, so the type
     * of a class constant became `? int`.
     */
    public function testNullableConstantType(): void
    {
        $this->assertChanges('token-replacement/union-null-constant-pre', 'token-replacement/union-null-constant-post');
    }

    /**
     * `ParameterExtractor` counted the `]` of an attribute as a closing bracket but its `#[` not as an opening one, so
     * all parameters after the first one with an attribute were taken for one, and weren't sorted.
     */
    public function testAttributedParametersAreSorted(): void
    {
        $this->assertChanges('sorting/attributed-parameters-pre', 'sorting/attributed-parameters-post');
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
