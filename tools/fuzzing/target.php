<?php declare(strict_types=1);

/** @var PhpFuzzer\Fuzzer $fuzzer */

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitor;

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

$lexer = new PhpParser\Lexer();
$parser = new PhpParser\Parser\Php7($lexer);
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


    private $tokens;
    public $hasProblematicConstruct;

    public function setTokens(array $tokens): void {
        $this->tokens = $tokens;
    }

    public function beforeTraverse(array $nodes): void {
        $this->hasProblematicConstruct = false;
    }

    public function leaveNode(PhpParser\Node $node) {
        // We don't precisely preserve nop statements.
        if ($node instanceof Stmt\Nop) {
            return NodeVisitor::REMOVE_NODE;
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

        // The parser does not distinguish between use X and use \X, as they are semantically
        // equivalent. However, use \keyword is legal PHP, while use keyword is not, so we inspect
        // tokens to detect this situation here.
        if ($node instanceof Stmt\Use_ && $node->uses[0]->name->isUnqualified() &&
            $this->tokens[$node->uses[0]->name->getStartTokenPos()]->is(\T_NAME_FULLY_QUALIFIED)
        ) {
            $this->hasProblematicConstruct = true;
        }
        if ($node instanceof Stmt\GroupUse && $node->prefix->isUnqualified() &&
            $this->tokens[$node->prefix->getStartTokenPos()]->is(\T_NAME_FULLY_QUALIFIED)
        ) {
            $this->hasProblematicConstruct = true;
        }

        // clone($x, ) is not preserved precisely.
        if ($node instanceof Expr\FuncCall && $node->name instanceof Node\Name &&
            $node->name->toLowerString() == 'clone' && count($node->args) == 1
        ) {
            $this->hasProblematicConstruct = true;
        }
    }
};
$traverser = new PhpParser\NodeTraverser();
$traverser->addVisitor($visitor);

$fuzzer->setTarget(function(string $input) use($lexer, $parser, $prettyPrinter, $nodeDumper, $visitor, $traverser) {
    $stmts = $parser->parse($input);
    $printed = $prettyPrinter->prettyPrintFile($stmts);

    $visitor->setTokens($parser->getTokens());
    $stmts = $traverser->traverse($stmts);
    if ($visitor->hasProblematicConstruct) {
        return;
    }

    try {
        $printedStmts = $parser->parse($printed);
    } catch (PhpParser\Error $e) {
        throw new Error("Failed to parse pretty printer output");
    }

    $visitor->setTokens($parser->getTokens());
    $printedStmts = $traverser->traverse($printedStmts);
    $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($printedStmts);
    if (!$same && !preg_match('/<\?php<\?php/i', $input)) {
        throw new Error("Result after pretty printing differs");
    }
});

$fuzzer->setMaxLen(1024);
$fuzzer->addDictionary(__DIR__ . '/php.dict');
$fuzzer->setAllowedExceptions([PhpParser\Error::class]);
