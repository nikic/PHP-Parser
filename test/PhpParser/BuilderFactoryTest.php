<?php

namespace PhpParser;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;

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
        return array(
            array('namespace', 'PhpParser\Builder\Namespace_'),
            array('class',     'PhpParser\Builder\Class_'),
            array('interface', 'PhpParser\Builder\Interface_'),
            array('trait',     'PhpParser\Builder\Trait_'),
            array('method',    'PhpParser\Builder\Method'),
            array('function',  'PhpParser\Builder\Function_'),
            array('property',  'PhpParser\Builder\Property'),
            array('param',     'PhpParser\Builder\Param'),
            array('use',       'PhpParser\Builder\Use_'),
        );
    }

    public function testNonExistingMethod() {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Method "foo" does not exist');
        $factory = new BuilderFactory();
        $factory->foo();
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
                ->addStmt($factory->method('methodWithTryCatch')
                    ->addStmt(
                            new Node\Stmt\TryCatch(array(
                                    new Node\Stmt\Foreach_(
                                        new Expr\Variable('array'), new Expr\Variable('value'), array(
                                            'stmts' => array(
                                                new Node\Stmt\If_(
                                                    new Expr\Isset_(array(new Expr\Variable("value['sasa']"))),
                                                    array('stmts' => array(
                                                            new Expr\Print_(new Expr\Variable('value'))),
                                                        'else' => new Node\Stmt\Else_(array(
                                                                new Node\Stmt\Continue_()))))))),
                            new Expr\Print_(new Expr\Variable('someParam'))),
                        array(new Node\Stmt\Catch_(new Node\Name('Exception'), 'someVar', 
                                array(new Node\Stmt\Throw_(new Node\Expr\New_(new Node\Name('Exception')))))))))
                ->addStmt($factory->property('someProperty')->makeProtected())
                ->addStmt($factory->property('someStaticProperty')->makeStatic())
                ->addStmt($factory->property('anotherProperty')
                    ->makePrivate()
                    ->setDefault(array(1, 2, 3))))
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
    public static $someStaticProperty;
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
    public function methodWithTryCatch()
    {
        try {
            foreach ($array as $value) {
                if (isset($value['sasa'])) {
                    print $value;
                } else {
                    continue;
                }
            }
            print $someParam;
        } catch (Exception $someVar) {
            throw new Exception();
        }
    }
}
EOC;

        $stmts = array($node);
        $prettyPrinter = new PrettyPrinter\Standard();
        $generated = $prettyPrinter->prettyPrintFile($stmts);

        $this->assertEquals(
            str_replace("\r\n", "\n", $expected),
            str_replace("\r\n", "\n", $generated)
        );
    }
}
