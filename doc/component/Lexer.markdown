Lexer component documentation
=============================

The lexer is responsible for providing tokens to the parser. The project comes with two lexers: `PhpParser\Lexer` and
`PhpParser\Lexer\Emulative`. The latter is an extension of the former, which adds the ability to emulate tokens of
newer PHP versions and thus allows parsing of new code on older versions.

A lexer has to define the following public interface:

    void startLexing(string $code);
    string handleHaltCompiler();
    int getNextToken(string &$value = null, array &$startAttributes = null, array &$endAttributes = null);

The `startLexing()` method is invoked with the source code that is to be lexed (including the opening tag) whenever the
`parse()` method of the parser is called. It can be used to reset state or preprocess the source code or tokens.

The `handleHaltCompiler()` method is called whenever a `T_HALT_COMPILER` token is encountered. It has to return the
remaining string after the construct (not including `();`).

The `getNextToken()` method returns the ID of the next token (as defined by the `Parser::T_*` constants). If no more
tokens are available it must return `0`, which is the ID of the `EOF` token. Furthermore the string content of the
token should be written into the by-reference `$value` parameter (which will then be available as `$n` in the parser).

Attribute handling
------------------

The other two by-ref variables `$startAttributes` and `$endAttributes` define which attributes will eventually be
assigned to the generated nodes: The parser will take the `$startAttributes` from the first token which is part of the
node and the `$endAttributes` from the last token that is part of the node.

E.g. if the tokens `T_FUNCTION T_STRING ... '{' ... '}'` constitute a node, then the `$startAttributes` from the
`T_FUNCTION` token will be taken and the `$endAttributes` from the `'}'` token.

By default the lexer creates the attributes `startLine`, `comments` (both part of `$startAttributes`) and `endLine`
(part of `$endAttributes`).

If you don't want all these attributes to be added (to reduce memory usage of the AST) you can simply remove them by
overriding the method:

```php
<?php

class LessAttributesLexer extends PhpParser\Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        // only keep startLine attribute
        unset($startAttributes['comments']);
        unset($endAttributes['endLine']);

        return $tokenId;
    }
}
```

Token offset lexer
------------------

A useful application for custom attributes is the token offset lexer, which provides the start and end token for a node
as attributes:

```php
<?php

class TokenOffsetLexer extends PhpParser\Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);
        $startAttributes['startOffset'] = $endAttributes['endOffset'] = $this->pos;
        return $tokenId;
    }

    public function getTokens() {
        return $this->tokens;
    }
}
```

This information can now be used to examine the exact formatting used for a node. For example the AST does not
distinguish whether a property was declared using `public` or using `var`, but you can retrieve this information based
on the token offset:

```php
function isDeclaredUsingVar(array $tokens, PhpParser\Node\Stmt\Property $prop) {
    $i = $prop->getAttribute('startOffset');
    return $tokens[$i][0] === T_VAR;
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

$lexer = new TokenOffsetLexer();
$parser = new PhpParser\Parser($lexer);

$visitor = new MyNodeVisitor();
$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor($visitor);

try {
    $stmts = $parser->parse($code);
    $visitor->setTokens($lexer->getTokens());
    $stmts = $traverser->traverse($stmts);
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
```

The same approach can also be used to perform specific modifications in the code, without changing the formatting in
other places (which is the case when using the pretty printer).
