<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class NameResolverTest extends \PHPUnit\Framework\TestCase {
    private function canonicalize($string) {
        return str_replace("\r\n", "\n", $string);
    }

    /**
     * @covers \PhpParser\NodeVisitor\NameResolver
     */
    public function testResolveNames(): void {
        $code = <<<'EOC'
<?php

namespace Foo {
    use Hallo as Hi;

    new Bar();
    new Hi();
    new Hi\Bar();
    new \Bar();
    new namespace\Bar();

    bar();
    hi();
    Hi\bar();
    foo\bar();
    \bar();
    namespace\bar();
}
namespace {
    use Hallo as Hi;

    new Bar();
    new Hi();
    new Hi\Bar();
    new \Bar();
    new namespace\Bar();

    bar();
    hi();
    Hi\bar();
    foo\bar();
    \bar();
    namespace\bar();
}
namespace Bar {
    use function foo\bar as baz;
    use const foo\BAR as BAZ;
    use foo as bar;

    bar();
    baz();
    bar\foo();
    baz\foo();
    BAR();
    BAZ();
    BAR\FOO();
    BAZ\FOO();

    bar;
    baz;
    bar\foo;
    baz\foo;
    BAR;
    BAZ;
    BAR\FOO;
    BAZ\FOO;
}
namespace Baz {
    use A\T\{B\C, D\E};
    use function X\T\{b\c, d\e};
    use const Y\T\{B\C, D\E};
    use Z\T\{G, function f, const K};

    new C;
    new E;
    new C\D;
    new E\F;
    new G;

    c();
    e();
    f();
    C;
    E;
    K;
}
EOC;
        $expectedCode = <<<'EOC'
namespace Foo {
    use Hallo as Hi;
    new \Foo\Bar();
    new \Hallo();
    new \Hallo\Bar();
    new \Bar();
    new \Foo\Bar();
    bar();
    hi();
    \Hallo\bar();
    \Foo\foo\bar();
    \bar();
    \Foo\bar();
}
namespace {
    use Hallo as Hi;
    new \Bar();
    new \Hallo();
    new \Hallo\Bar();
    new \Bar();
    new \Bar();
    \bar();
    \hi();
    \Hallo\bar();
    \foo\bar();
    \bar();
    \bar();
}
namespace Bar {
    use function foo\bar as baz;
    use const foo\BAR as BAZ;
    use foo as bar;
    bar();
    \foo\bar();
    \foo\foo();
    \Bar\baz\foo();
    BAR();
    \foo\bar();
    \foo\FOO();
    \Bar\BAZ\FOO();
    bar;
    baz;
    \foo\foo;
    \Bar\baz\foo;
    BAR;
    \foo\BAR;
    \foo\FOO;
    \Bar\BAZ\FOO;
}
namespace Baz {
    use A\T\{B\C, D\E};
    use function X\T\{b\c, d\e};
    use const Y\T\{B\C, D\E};
    use Z\T\{G, function f, const K};
    new \A\T\B\C();
    new \A\T\D\E();
    new \A\T\B\C\D();
    new \A\T\D\E\F();
    new \Z\T\G();
    \X\T\b\c();
    \X\T\d\e();
    \Z\T\f();
    \Y\T\B\C;
    \Y\T\D\E;
    \Z\T\K;
}
EOC;

        $prettyPrinter = new PhpParser\PrettyPrinter\Standard();
        $stmts = $this->parseAndResolve($code);

        $this->assertSame(
            $this->canonicalize($expectedCode),
            $prettyPrinter->prettyPrint($stmts)
        );
    }

    /**
     * @covers \PhpParser\NodeVisitor\NameResolver
     */
    public function testResolveLocations(): void {
        $code = <<<'EOC'
<?php
namespace NS;

#[X]
class A extends B implements C, D {
    use E, F, G {
        f as private g;
        E::h as i;
        E::j insteadof F, G;
    }

    #[X]
    public float $php = 7.4;
    public ?Foo $person;
    protected static ?bool $probability;
    public A|B|int $prop;

    #[X]
    const C = 1;
    
    public const X A = X::Bar;
    public const X\Foo B = X\Foo::Bar;
    public const \X\Foo C = \X\Foo::Bar;

    public Foo $foo {
        #[X]
        set(#[X] Bar $v) {}
    }

    public function __construct(
        public Foo $bar {
            #[X]
            set(#[X] Bar $v) {}
        }
    ) {}
}

#[X]
interface A extends C, D {
    public function a(A $a) : A;
    public function b(A|B|int $a): A|B|int;
    public function c(A&B $a): A&B;
}

#[X]
enum E: int {
    #[X]
    case A = 1;
}

#[X]
trait A {}

#[X]
function f(#[X] A $a) : A {}
function f2(array $a) : array {}
function fn3(?A $a) : ?A {}
function fn4(?array $a) : ?array {}

#[X]
function(A $a) : A {};

#[X]
fn(array $a): array => $a;
fn(A $a): A => $a;
fn(?A $a): ?A => $a;

#[X]
const EXAMPLE = true;

A::b();
A::$b;
A::B;
new A;
$a instanceof A;

namespace\a();
namespace\A;

try {
    $someThing;
} catch (A $a) {
    $someThingElse;
}
EOC;
        $expectedCode = <<<'EOC'
namespace NS;

#[\NS\X]
class A extends \NS\B implements \NS\C, \NS\D
{
    use \NS\E, \NS\F, \NS\G {
        f as private g;
        \NS\E::h as i;
        \NS\E::j insteadof \NS\F, \NS\G;
    }
    #[\NS\X]
    public float $php = 7.4;
    public ?\NS\Foo $person;
    protected static ?bool $probability;
    public \NS\A|\NS\B|int $prop;
    #[\NS\X]
    const C = 1;
    public const \NS\X A = \NS\X::Bar;
    public const \NS\X\Foo B = \NS\X\Foo::Bar;
    public const \X\Foo C = \X\Foo::Bar;
    public \NS\Foo $foo {
        #[\NS\X]
        set(
            #[\NS\X]
            \NS\Bar $v
        ) {
        }
    }
    public function __construct(public \NS\Foo $bar {
        #[\NS\X]
        set(
            #[\NS\X]
            \NS\Bar $v
        ) {
        }
    })
    {
    }
}
#[\NS\X]
interface A extends \NS\C, \NS\D
{
    public function a(\NS\A $a): \NS\A;
    public function b(\NS\A|\NS\B|int $a): \NS\A|\NS\B|int;
    public function c(\NS\A&\NS\B $a): \NS\A&\NS\B;
}
#[\NS\X]
enum E : int
{
    #[\NS\X]
    case A = 1;
}
#[\NS\X]
trait A
{
}
#[\NS\X]
function f(
    #[\NS\X]
    \NS\A $a
): \NS\A
{
}
function f2(array $a): array
{
}
function fn3(?\NS\A $a): ?\NS\A
{
}
function fn4(?array $a): ?array
{
}
#[\NS\X] function (\NS\A $a): \NS\A {
};
#[\NS\X] fn(array $a): array => $a;
fn(\NS\A $a): \NS\A => $a;
fn(?\NS\A $a): ?\NS\A => $a;
#[\NS\X]
const EXAMPLE = true;
\NS\A::b();
\NS\A::$b;
\NS\A::B;
new \NS\A();
$a instanceof \NS\A;
\NS\a();
\NS\A;
try {
    $someThing;
} catch (\NS\A $a) {
    $someThingElse;
}
EOC;

        $prettyPrinter = new PhpParser\PrettyPrinter\Standard();
        $stmts = $this->parseAndResolve($code);

        $this->assertSame(
            $this->canonicalize($expectedCode),
            $prettyPrinter->prettyPrint($stmts)
        );
    }

    public function testNoResolveSpecialName(): void {
        $stmts = [new Node\Expr\New_(new Name('self'))];

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testAddDeclarationNamespacedName(): void {
        $nsStmts = [
            new Stmt\Class_('A'),
            new Stmt\Interface_('B'),
            new Stmt\Function_('C'),
            new Stmt\Const_([
                new Node\Const_('D', new Node\Scalar\Int_(42))
            ]),
            new Stmt\Trait_('E'),
            new Expr\New_(new Stmt\Class_(null)),
            new Stmt\Enum_('F'),
        ];

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $stmts = $traverser->traverse([new Stmt\Namespace_(new Name('NS'), $nsStmts)]);
        $this->assertSame('NS\\A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertSame('NS\\B', (string) $stmts[0]->stmts[1]->namespacedName);
        $this->assertSame('NS\\C', (string) $stmts[0]->stmts[2]->namespacedName);
        $this->assertSame('NS\\D', (string) $stmts[0]->stmts[3]->consts[0]->namespacedName);
        $this->assertSame('NS\\E', (string) $stmts[0]->stmts[4]->namespacedName);
        $this->assertNull($stmts[0]->stmts[5]->class->namespacedName);
        $this->assertSame('NS\\F', (string) $stmts[0]->stmts[6]->namespacedName);

        $stmts = $traverser->traverse([new Stmt\Namespace_(null, $nsStmts)]);
        $this->assertSame('A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertSame('B', (string) $stmts[0]->stmts[1]->namespacedName);
        $this->assertSame('C', (string) $stmts[0]->stmts[2]->namespacedName);
        $this->assertSame('D', (string) $stmts[0]->stmts[3]->consts[0]->namespacedName);
        $this->assertSame('E', (string) $stmts[0]->stmts[4]->namespacedName);
        $this->assertNull($stmts[0]->stmts[5]->class->namespacedName);
        $this->assertSame('F', (string) $stmts[0]->stmts[6]->namespacedName);
    }

    public function testAddRuntimeResolvedNamespacedName(): void {
        $stmts = [
            new Stmt\Namespace_(new Name('NS'), [
                new Expr\FuncCall(new Name('foo')),
                new Expr\ConstFetch(new Name('FOO')),
            ]),
            new Stmt\Namespace_(null, [
                new Expr\FuncCall(new Name('foo')),
                new Expr\ConstFetch(new Name('FOO')),
            ]),
        ];

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $stmts = $traverser->traverse($stmts);

        $this->assertSame('NS\\foo', (string) $stmts[0]->stmts[0]->name->getAttribute('namespacedName'));
        $this->assertSame('NS\\FOO', (string) $stmts[0]->stmts[1]->name->getAttribute('namespacedName'));

        $this->assertFalse($stmts[1]->stmts[0]->name->hasAttribute('namespacedName'));
        $this->assertFalse($stmts[1]->stmts[1]->name->hasAttribute('namespacedName'));
    }

    /**
     * @dataProvider provideTestError
     */
    public function testError(Node $stmt, $errorMsg): void {
        $this->expectException(\PhpParser\Error::class);
        $this->expectExceptionMessage($errorMsg);

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->traverse([$stmt]);
    }

    public static function provideTestError() {
        return [
            [
                new Stmt\Use_([
                    new Node\UseItem(new Name('A\B'), 'B', 0, ['startLine' => 1]),
                    new Node\UseItem(new Name('C\D'), 'B', 0, ['startLine' => 2]),
                ], Stmt\Use_::TYPE_NORMAL),
                'Cannot use C\D as B because the name is already in use on line 2'
            ],
            [
                new Stmt\Use_([
                    new Node\UseItem(new Name('a\b'), 'b', 0, ['startLine' => 1]),
                    new Node\UseItem(new Name('c\d'), 'B', 0, ['startLine' => 2]),
                ], Stmt\Use_::TYPE_FUNCTION),
                'Cannot use function c\d as B because the name is already in use on line 2'
            ],
            [
                new Stmt\Use_([
                    new Node\UseItem(new Name('A\B'), 'B', 0, ['startLine' => 1]),
                    new Node\UseItem(new Name('C\D'), 'B', 0, ['startLine' => 2]),
                ], Stmt\Use_::TYPE_CONSTANT),
                'Cannot use const C\D as B because the name is already in use on line 2'
            ],
            [
                new Expr\New_(new Name\FullyQualified('self', ['startLine' => 3])),
                "'\\self' is an invalid class name on line 3"
            ],
            [
                new Expr\New_(new Name\Relative('self', ['startLine' => 3])),
                "'\\self' is an invalid class name on line 3"
            ],
            [
                new Expr\New_(new Name\FullyQualified('PARENT', ['startLine' => 3])),
                "'\\PARENT' is an invalid class name on line 3"
            ],
            [
                new Expr\New_(new Name\Relative('STATIC', ['startLine' => 3])),
                "'\\STATIC' is an invalid class name on line 3"
            ],
        ];
    }

    public function testClassNameIsCaseInsensitive(): void {
        $source = <<<'EOC'
<?php
namespace Foo;
use Bar\Baz;
$test = new baz();
EOC;

        $parser = new PhpParser\Parser\Php7(new PhpParser\Lexer\Emulative());
        $stmts = $parser->parse($source);

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $stmts = $traverser->traverse($stmts);
        $stmt = $stmts[0];

        $assign = $stmt->stmts[1]->expr;
        $this->assertSame('Bar\\Baz', $assign->expr->class->name);
    }

    public function testSpecialClassNamesAreCaseInsensitive(): void {
        $source = <<<'EOC'
<?php
namespace Foo;

class Bar
{
    public static function method()
    {
        SELF::method();
        PARENT::method();
        STATIC::method();
    }
}
EOC;

        $parser = new PhpParser\Parser\Php7(new PhpParser\Lexer\Emulative());
        $stmts = $parser->parse($source);

        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $stmts = $traverser->traverse($stmts);
        $classStmt = $stmts[0];
        $methodStmt = $classStmt->stmts[0]->stmts[0];

        $this->assertSame('SELF', (string) $methodStmt->stmts[0]->expr->class);
        $this->assertSame('PARENT', (string) $methodStmt->stmts[1]->expr->class);
        $this->assertSame('STATIC', (string) $methodStmt->stmts[2]->expr->class);
    }

    public function testAddOriginalNames(): void {
        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver(null, ['preserveOriginalNames' => true]));

        $n1 = new Name('Bar');
        $n2 = new Name('bar');
        $origStmts = [
            new Stmt\Namespace_(new Name('Foo'), [
                new Expr\ClassConstFetch($n1, 'FOO'),
                new Expr\FuncCall($n2),
            ])
        ];

        $stmts = $traverser->traverse($origStmts);

        $this->assertSame($n1, $stmts[0]->stmts[0]->class->getAttribute('originalName'));
        $this->assertSame($n2, $stmts[0]->stmts[1]->name->getAttribute('originalName'));
    }

    public function testAttributeOnlyMode(): void {
        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver(null, ['replaceNodes' => false]));

        $n1 = new Name('Bar');
        $n2 = new Name('bar');
        $origStmts = [
            new Stmt\Namespace_(new Name('Foo'), [
                new Expr\ClassConstFetch($n1, 'FOO'),
                new Expr\FuncCall($n2),
            ])
        ];

        $traverser->traverse($origStmts);

        $this->assertEquals(
            new Name\FullyQualified('Foo\Bar'), $n1->getAttribute('resolvedName'));
        $this->assertFalse($n2->hasAttribute('resolvedName'));
        $this->assertEquals(
            new Name\FullyQualified('Foo\bar'), $n2->getAttribute('namespacedName'));
    }

    private function parseAndResolve(string $code): array {
        $parser = new PhpParser\Parser\Php8(new PhpParser\Lexer\Emulative());
        $traverser = new PhpParser\NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $stmts = $parser->parse($code);
        return $traverser->traverse($stmts);
    }
}
