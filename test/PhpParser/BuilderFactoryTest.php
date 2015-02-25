<?php

namespace PhpParser;

use PhpParser\Node\Expr;

class BuilderFactoryTest extends \PHPUnit_Framework_TestCase
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
        );
    }

    public function testIntegration() {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Name\Space')
            ->addStmt($factory
                ->class('SomeClass')
                ->extend('SomeOtherClass')
                ->implement('A\Few', '\Interfaces')
                ->makeAbstract()

                ->addStmt($factory->method('someMethod')
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

abstract class SomeClass extends SomeOtherClass implements A\Few, \Interfaces
{
    protected $someProperty;
    public static $someStaticProperty;
    private $anotherProperty = array(1, 2, 3);
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
