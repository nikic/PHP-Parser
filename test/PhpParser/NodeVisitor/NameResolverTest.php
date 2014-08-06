<?php

namespace PhpParser\NodeVisitor;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class NameResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers NameResolver
     */
    public function testResolveNames() {
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
    bar();
    hi();
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
EOC;

        $parser        = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $prettyPrinter = new PhpParser\PrettyPrinter\Standard;
        $traverser     = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        $this->assertEquals($expectedCode, $prettyPrinter->prettyPrint($stmts));
    }

    /**
     * @covers NameResolver
     */
    public function testResolveLocations() {
        $code = <<<'EOC'
<?php
namespace NS;

class A extends B implements C {
    use A;
}

interface A extends C {
    public function a(A $a);
}

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

class A extends \NS\B implements \NS\C
{
    use \NS\A;
}
interface A extends \NS\C
{
    public function a(\NS\A $a);
}
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

        $parser        = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $prettyPrinter = new PhpParser\PrettyPrinter\Standard;
        $traverser     = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        $this->assertEquals($expectedCode, $prettyPrinter->prettyPrint($stmts));
    }

    public function testNoResolveSpecialName() {
        $stmts = array(new Node\Expr\New_(new Name('self')));

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    protected function createNamespacedAndNonNamespaced(array $stmts) {
        return array(
            new Stmt\Namespace_(new Name('NS'), $stmts),
            new Stmt\Namespace_(null,                          $stmts),
        );
    }

    public function testAddNamespacedName() {
        $stmts = $this->createNamespacedAndNonNamespaced(array(
            new Stmt\Class_('A'),
            new Stmt\Interface_('B'),
            new Stmt\Function_('C'),
            new Stmt\Const_(array(
                new Node\Const_('D', new Node\Scalar\String('E'))
            )),
        ));

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $traverser->traverse($stmts);

        $this->assertEquals('NS\\A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertEquals('NS\\B', (string) $stmts[0]->stmts[1]->namespacedName);
        $this->assertEquals('NS\\C', (string) $stmts[0]->stmts[2]->namespacedName);
        $this->assertEquals('NS\\D', (string) $stmts[0]->stmts[3]->consts[0]->namespacedName);
        $this->assertEquals('A',     (string) $stmts[1]->stmts[0]->namespacedName);
        $this->assertEquals('B',     (string) $stmts[1]->stmts[1]->namespacedName);
        $this->assertEquals('C',     (string) $stmts[1]->stmts[2]->namespacedName);
        $this->assertEquals('D',     (string) $stmts[1]->stmts[3]->consts[0]->namespacedName);
    }

    public function testAddTraitNamespacedName() {
        $stmts = $this->createNamespacedAndNonNamespaced(array(
            new Stmt\Trait_('A')
        ));

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $traverser->traverse($stmts);

        $this->assertEquals('NS\\A', (string) $stmts[0]->stmts[0]->namespacedName);
        $this->assertEquals('A',     (string) $stmts[1]->stmts[0]->namespacedName);
    }

    /**
     * @dataProvider provideTestAlreadyInUseError
     */
    public function testAlreadyInUseError(Stmt\Use_ $use, $errorMsg) {
        $this->setExpectedException('PhpParser\Error', $errorMsg);

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->traverse(array($use));
    }

    public function provideTestAlreadyInUseError() {
        return array(
            array(
                new Stmt\Use_(array(
                    new Stmt\UseUse(new Name('A\B'), 'B', array('startLine' => 1)),
                    new Stmt\UseUse(new Name('C\D'), 'B', array('startLine' => 2)),
                ), Stmt\Use_::TYPE_NORMAL),
                'Cannot use C\D as B because the name is already in use on line 2'
            ),
            array(
                new Stmt\Use_(array(
                    new Stmt\UseUse(new Name('a\b'), 'b', array('startLine' => 1)),
                    new Stmt\UseUse(new Name('c\d'), 'B', array('startLine' => 2)),
                ), Stmt\Use_::TYPE_FUNCTION),
                'Cannot use function c\d as B because the name is already in use on line 2'
            ),
            array(
                new Stmt\Use_(array(
                    new Stmt\UseUse(new Name('A\B'), 'B', array('startLine' => 1)),
                    new Stmt\UseUse(new Name('C\D'), 'B', array('startLine' => 2)),
                ), Stmt\Use_::TYPE_CONSTANT),
                'Cannot use const C\D as B because the name is already in use on line 2'
            ),
        );
    }

    public function testClassNameIsCaseInsensitive()
    {
        $source = <<<'EOC'
<?php
namespace Foo;
use Bar\Baz;
$test = new baz();
EOC;

        $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $stmts = $parser->parse($source);

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $traverser->traverse($stmts);
        $stmt = $stmts[0];

        $this->assertEquals(array('Bar', 'Baz'), $stmt->stmts[1]->expr->class->parts);
    }

    public function testSpecialClassNamesAreCaseInsensitive()
    {
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

        $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $stmts = $parser->parse($source);

        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor(new NameResolver);

        $stmts = $traverser->traverse($stmts);
        $stmt = $stmts[0];
        $methodStmt = $stmt->stmts[1]->stmts[1];
        
        $this->assertEquals('SELF', (string)$methodStmt->stmts[0]->class);
        $this->assertEquals('PARENT', (string)$methodStmt->stmts[1]->class);
        $this->assertEquals('STATIC', (string)$methodStmt->stmts[2]->class);
    }
}
