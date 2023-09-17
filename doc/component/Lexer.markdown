Lexer component documentation
=============================

The lexer is responsible for providing tokens to the parser. Typical use of the library does not require direct
interaction with the lexer, as an appropriate lexer is created by `PhpParser\ParserFactory`. The tokens produced
by the lexer can then be retrieved using `PhpParser\Parser::getTokens()`.

Emulation
---------

While this library implements a custom parser, it relies on PHP's `ext/tokenizer` extension to perform lexing. However,
this extension only supports lexing code for the PHP version you are running on, while this library also wants to support
parsing newer code. For that reason, the lexer performs additional "emulation" in three layers:

First, PhpParser uses the `PhpToken` based representation introduced in PHP 8.0, rather than the array-based tokens from
previous versions. The `PhpParser\Token` class either extends `PhpToken` (on PHP 8.0) or a polyfill implementation. The
polyfill implementation will also perform two emulations that are required by the parser and cannot be disabled:

 * Single-line comments use the PHP 8.0 representation that does not include a trailing newline. The newline will be
   part of a following `T_WHITESPACE` token.
 * Namespaced names use the PHP 8.0 representation using `T_NAME_FULLY_QUALIFIED`, `T_NAME_QUALIFIED` and
   `T_NAME_RELATIVE` tokens, rather than the previous representation using a sequence of `T_STRING` and `T_NS_SEPARATOR`.
   This means that certain code that is legal on older versions (namespaced names including whitespace, such as `A \ B`)
   will not be accepted by the parser.

Second, the `PhpParser\Lexer` base class will convert `&` tokens into the PHP 8.1 representation of either
`T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG` or `T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG`. This is required by the parser
and cannot be disabled.

Finally, `PhpParser\Lexer\Emulative` performs other, optional emulations. This lexer is parameterized by `PhpVersion`
and will try to emulate `ext/tokenizer` output for that version. This is done using separate `TokenEmulator`s for each
emulated feature.

Emulation is usually used to support newer PHP versions, but there is also very limited support for reverse emulation to
older PHP versions, which can make keywords from newer versions non-reserved.

Tokens, positions and attributes
--------------------------------

The `Lexer::tokenize()` method returns an array of `PhpParser\Token`s. The most important parts of the interface can be
summarized as follows:

```php
class Token {
    /** @var int Token ID, either T_* or ord($char) for single-character tokens. */
    public int $id;
    /** @var string The textual content of the token. */
    public string $text;
    /** @var int The 1-based starting line of the token (or -1 if unknown). */
    public int $line;
    /** @var int The 0-based starting position of the token (or -1 if unknown). */
    public int $pos;

    /** @param int|string|(int|string)[] $kind Token ID or text (or array of them) */
    public function is($kind): bool;
}
```

Unlike PHP's own `PhpToken::tokenize()` output, the token array is terminated by a sentinel token with ID 0.

The lexer is normally invoked implicitly by the parser. In that case, the tokens for the last parse can be retrieved
using `Parser::getTokens()`.

Nodes in the AST produced by the parser always corresponds to some range of tokens. The parser adds a number of
positioning attributes to allow mapping nodes back to lines, tokens or file offsets:

 * `startLine`: Line in which the node starts. Used by `$node->getStartLine()`.
 * `endLine`: Line in which the node ends. Used by `$node->getEndLine()`.
 * `startTokenPos`: Offset into the token array of the first token in the node. Used by `$node->getStartTokenPos()`.
 * `endTokenPos`: Offset into the token array of the last token in the node. Used by `$node->getEndTokenPos()`.
 * `startFilePos`: Offset into the code string of the first character that is part of the node. Used by `$node->getStartFilePos()`.
 * `endFilePos`: Offset into the code string of the last character that is part of the node. Used by `$node->getEndFilePos()`.

Note that `start`/`end` here are closed rather than half-open ranges. This means that a node consisting of a single
token will have `startTokenPos == endTokenPos` rather than `startTokenPos + 1 == endTokenPos`. This also means that a
zero-length node will have `startTokenPos -1 == endTokenPos`.

### Using token positions

> **Note:** The example in this section is outdated in that this information is directly available in the AST: While
> `$property->isPublic()` does not distinguish between `public` and `var`, directly checking `$property->flags` for
> the `$property->flags & Class_::VISIBILITY_MODIFIER_MASK) === 0` allows making this distinction without resorting to
> tokens. However, the general idea behind the example still applies in other cases.

The token offset information is useful if you wish to examine the exact formatting used for a node. For example the AST
does not distinguish whether a property was declared using `public` or using `var`, but you can retrieve this
information based on the token position:

```php
/** @param PhpParser\Token[] $tokens */
function isDeclaredUsingVar(array $tokens, PhpParser\Node\Stmt\Property $prop) {
    $i = $prop->getStartTokenPos();
    return $tokens[$i]->id === T_VAR;
}
```

In order to make use of this function, you will have to provide the tokens from the lexer to your node visitor using
code similar to the following:

```php
class MyNodeVisitor extends PhpParser\NodeVisitorAbstract {
    private $tokens;
    public function setTokens(array $tokens) {
        $this->tokens = $tokens;
    }

    public function leaveNode(PhpParser\Node $node) {
        if ($node instanceof PhpParser\Node\Stmt\Property) {
            var_dump(isDeclaredUsingVar($this->tokens, $node));
        }
    }
}

$parser = (new PhpParser\ParserFactory())->createForHostVersion($lexerOptions);

$visitor = new MyNodeVisitor();
$traverser = new PhpParser\NodeTraverser($visitor);

try {
    $stmts = $parser->parse($code);
    $visitor->setTokens($parser->getTokens());
    $stmts = $traverser->traverse($stmts);
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```
