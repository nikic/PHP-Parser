<?php declare(strict_types=1);

/** @var PhpFuzzer\Fuzzer $fuzzer */

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

if (class_exists(PhpParser\Parser\Php7::class)) {
    echo "The PHP-Parser target can only be used with php-fuzzer.phar,\n";
    echo "otherwise there is a conflict with php-fuzzer's own use of PHP-Parser.\n";
    exit(1);
}

$autoload = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "Cannot find PHP-Parser installation in " . __DIR__ . "/PHP-Parser\n";
    exit(1);
}

require $autoload;

$parser = new PhpParser\Parser\Php7(new PhpParser\Lexer);
$prettyPrinter = new PhpParser\PrettyPrinter\Standard();
$nodeDumper = new PhpParser\NodeDumper();
$visitor = new class extends PhpParser\NodeVisitorAbstract {
    private const CAST_NAMES = [
        'int', 'integer',
        'double', 'float', 'real',
        'string', 'binary',
        'array', 'object',
        'bool', 'boolean',
        'unset',
    ];

    public $hasProblematicConstruct;

    public function beforeTraverse(array $nodes) {
        $this->hasProblematicConstruct = false;
    }

    public function leaveNode(PhpParser\Node $node) {
        // We don't precisely preserve nop statements.
        if ($node instanceof Stmt\Nop) {
            return PhpParser\NodeTraverser::REMOVE_NODE;
        }

        // We don't precisely preserve redundant trailing commas in array destructuring.
        if ($node instanceof Expr\List_) {
            while (!empty($node->items) && $node->items[count($node->items) - 1] === null) {
                array_pop($node->items);
            }
        }

        // For T_NUM_STRING the parser produced negative integer literals. Convert these into
        // a unary minus followed by a positive integer.
        if ($node instanceof Scalar\Int_ && $node->value < 0) {
            if ($node->value === \PHP_INT_MIN) {
                // PHP_INT_MIN == -PHP_INT_MAX - 1
                return new Expr\BinaryOp\Minus(
                    new Expr\UnaryMinus(new Scalar\Int_(\PHP_INT_MAX)),
                    new Scalar\Int_(1));
            }
            return new Expr\UnaryMinus(new Scalar\Int_(-$node->value));
        }

        // If a constant with the same name as a cast operand occurs inside parentheses, it will
        // be parsed back as a cast. E.g. "foo(int)" will fail to parse, because the argument is
        // interpreted as a cast. We can run into this with inputs like "foo(int\n)", where the
        // newline is not preserved.
        if ($node instanceof Expr\ConstFetch && $node->name->isUnqualified() &&
            in_array($node->name->toLowerString(), self::CAST_NAMES)
        ) {
            $this->hasProblematicConstruct = true;
        }
    }
};
$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor($visitor);

$fuzzer->setTarget(function(string $input) use($parser, $prettyPrinter, $nodeDumper, $visitor, $traverser) {
    $stmts = $parser->parse($input);
    $printed = $prettyPrinter->prettyPrintFile($stmts);

    $stmts = $traverser->traverse($stmts);
    if ($visitor->hasProblematicConstruct) {
        return;
    }

    try {
        $printedStmts = $parser->parse($printed);
    } catch (PhpParser\Error $e) {
        throw new Error("Failed to parse pretty printer output");
    }

    $printedStmts = $traverser->traverse($printedStmts);
    $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($printedStmts);
    if (!$same && !preg_match('/<\?php<\?php/i', $input)) {
        throw new Error("Result after pretty printing differs");
    }
});

$fuzzer->setMaxLen(1024);
$fuzzer->addDictionary(__DIR__ . '/php.dict');
