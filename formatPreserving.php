<?php

namespace Foo;

use PhpParser;
use PhpParser\Node;

require __DIR__ . '/vendor/autoload.php';

$lexer = new PhpParser\Lexer\Emulative([
    'usedAttributes' => [
        'comments',
        'startLine', 'endLine',
        'startFilePos', 'endFilePos',
        'startTokenPos', 'endTokenPos',
    ],
]);

$parser = new PhpParser\Parser\Php7($lexer, [
        'useIdentifierNodes' => true,
        'useConsistentVariableNodes' => true,
    ]
);

$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor(new PhpParser\NodeVisitor\CloningVisitor());

$printer = new PhpParser\PrettyPrinter\Standard();

$code = <<<'PHP'
<?php

function foo() {
    echo
        1
            +
                2
                    +
                        3;

    echo "a";

    echo "b";
}
PHP;

$oldStmts = $parser->parse($code);
$oldTokens = $lexer->getTokens();

$newStmts = $traverser->traverse($oldStmts);

$newStmts[0]->stmts[0]->exprs[0]->left->right->value = 42;
$newStmts[0]->stmts[1] = new Node\Expr\Assign(new Node\Expr\Variable('a'), new Node\Scalar\LNumber(42));

$newCode = $printer->printFormatPreserving($newStmts, $oldStmts, $code, $oldTokens);
echo $newCode, "\n";