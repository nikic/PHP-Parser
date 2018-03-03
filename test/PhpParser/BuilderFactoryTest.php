<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Builder;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Tests\A;

class BuilderFactoryTest extends TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className) {
        $factory = new BuilderFactory;
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public function provideTestFactory() {
        return [
            ['namespace', Builder\Namespace_::class],
            ['class',     Builder\Class_::class],
            ['interface', Builder\Interface_::class],
            ['trait',     Builder\Trait_::class],
            ['method',    Builder\Method::class],
            ['function',  Builder\Function_::class],
            ['property',  Builder\Property::class],
            ['param',     Builder\Param::class],
            ['use',       Builder\Use_::class],
        ];
    }

    public function testVal() {
        // This method is a wrapper around BuilderHelpers::normalizeValue(),
        // which is already tested elsewhere
        $factory = new BuilderFactory();
        $this->assertEquals(
            new String_("foo"),
            $factory->val("foo")
        );
    }

    public function testConcat() {
        $factory = new BuilderFactory();
        $varA = new Expr\Variable('a');
        $varB = new Expr\Variable('b');
        $varC = new Expr\Variable('c');

        $this->assertEquals(
            new Concat($varA, $varB),
            $factory->concat($varA, $varB)
        );
        $this->assertEquals(
            new Concat(new Concat($varA, $varB), $varC),
            $factory->concat($varA, $varB, $varC)
        );
        $this->assertEquals(
            new Concat(new Concat(new String_("a"), $varB), new String_("c")),
            $factory->concat("a", $varB, "c")
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected at least two expressions
     */
    public function testConcatOneError() {
        (new BuilderFactory())->concat("a");
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected string or Expr
     */
    public function testConcatInvalidExpr() {
        (new BuilderFactory())->concat("a", 42);
    }

    public function testArgs() {
        $factory = new BuilderFactory();
        $unpack = new Arg(new Expr\Variable('c'), false, true);
        $this->assertEquals(
            [
                new Arg(new Expr\Variable('a')),
                new Arg(new String_('b')),
                $unpack
            ],
            $factory->args([new Expr\Variable('a'), 'b', $unpack])
        );
    }

    public function testCalls() {
        $factory = new BuilderFactory();

        // Simple function call
        $this->assertEquals(
            new Expr\FuncCall(
                new Name('var_dump'),
                [new Arg(new String_('str'))]
            ),
            $factory->funcCall('var_dump', ['str'])
        );
        // Dynamic function call
        $this->assertEquals(
            new Expr\FuncCall(new Expr\Variable('fn')),
            $factory->funcCall(new Expr\Variable('fn'))
        );

        // Simple method call
        $this->assertEquals(
            new Expr\MethodCall(
                new Expr\Variable('obj'),
                new Identifier('method'),
                [new Arg(new LNumber(42))]
            ),
            $factory->methodCall(new Expr\Variable('obj'), 'method', [42])
        );
        // Explicitly pass Identifier node
        $this->assertEquals(
            new Expr\MethodCall(
                new Expr\Variable('obj'),
                new Identifier('method')
            ),
            $factory->methodCall(new Expr\Variable('obj'), new Identifier('method'))
        );
        // Dynamic method call
        $this->assertEquals(
            new Expr\MethodCall(
                new Expr\Variable('obj'),
                new Expr\Variable('method')
            ),
            $factory->methodCall(new Expr\Variable('obj'), new Expr\Variable('method'))
        );

        // Simple static method call
        $this->assertEquals(
            new Expr\StaticCall(
                new Name\FullyQualified('Foo'),
                new Identifier('bar'),
                [new Arg(new Expr\Variable('baz'))]
            ),
            $factory->staticCall('\Foo', 'bar', [new Expr\Variable('baz')])
        );
        // Dynamic static method call
        $this->assertEquals(
            new Expr\StaticCall(
                new Expr\Variable('foo'),
                new Expr\Variable('bar')
            ),
            $factory->staticCall(new Expr\Variable('foo'), new Expr\Variable('bar'))
        );

        // Simple new call
        $this->assertEquals(
            new Expr\New_(new Name\FullyQualified('stdClass')),
            $factory->new('\stdClass')
        );
        // Dynamic new call
        $this->assertEquals(
            new Expr\New_(
                new Expr\Variable('foo'),
                [new Arg(new String_('bar'))]
            ),
            $factory->new(new Expr\Variable('foo'), ['bar'])
        );
    }

    public function testConstFetches() {
        $factory = new BuilderFactory();
        $this->assertEquals(
            new Expr\ConstFetch(new Name('FOO')),
            $factory->constFetch('FOO')
        );
        $this->assertEquals(
            new Expr\ClassConstFetch(new Name('Foo'), new Identifier('BAR')),
            $factory->classConstFetch('Foo', 'BAR')
        );
        $this->assertEquals(
            new Expr\ClassConstFetch(new Expr\Variable('foo'), new Identifier('BAR')),
            $factory->classConstFetch(new Expr\Variable('foo'), 'BAR')
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected string or instance of Node\Identifier
     */
    public function testInvalidIdentifier() {
        (new BuilderFactory())->classConstFetch('Foo', new Expr\Variable('foo'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Expected string or instance of Node\Identifier or Node\Expr
     */
    public function testInvalidIdentifierOrExpr() {
        (new BuilderFactory())->staticCall('Foo', new Name('bar'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Name must be a string or an instance of Node\Name or Node\Expr
     */
    public function testInvalidNameOrExpr() {
        (new BuilderFactory())->funcCall(new Node\Stmt\Return_());
    }

    public function testIntegration() {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Name\Space')
            ->addStmt($factory->use('Foo\Bar\SomeOtherClass'))
            ->addStmt($factory->use('Foo\Bar')->as('A'))
            ->addStmt($factory
                ->class('SomeClass')
                ->extend('SomeOtherClass')
                ->implement('A\Few', '\Interfaces')
                ->makeAbstract()

                ->addStmt($factory->method('firstMethod'))

                ->addStmt($factory->method('someMethod')
                    ->makePublic()
                    ->makeAbstract()
                    ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
                    ->setDocComment('/**
                                      * This method does something.
                                      *
                                      * @param SomeClass And takes a parameter
                                      */'))

                ->addStmt($factory->method('anotherMethod')
                    ->makeProtected()
                    ->addParam($factory->param('someParam')->setDefault('test'))
                    ->addStmt(new Expr\Print_(new Expr\Variable('someParam'))))

                ->addStmt($factory->property('someProperty')->makeProtected())
                ->addStmt($factory->property('anotherProperty')
                    ->makePrivate()
                    ->setDefault([1, 2, 3])))
            ->getNode()
        ;

        $expected = <<<'EOC'
<?php

namespace Name\Space;

use Foo\Bar\SomeOtherClass;
use Foo\Bar as A;
abstract class SomeClass extends SomeOtherClass implements A\Few, \Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    function firstMethod()
    {
    }
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
EOC;

        $stmts = [$node];
        $prettyPrinter = new PrettyPrinter\Standard();
        $generated = $prettyPrinter->prettyPrintFile($stmts);

        $this->assertEquals(
            str_replace("\r\n", "\n", $expected),
            str_replace("\r\n", "\n", $generated)
        );
    }
}
