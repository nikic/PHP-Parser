<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;

class BuilderFactoryTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className): void {
        $factory = new BuilderFactory();
        $this->assertInstanceOf($className, $factory->$methodName('test'));
    }

    public static function provideTestFactory() {
        return [
            ['namespace',   Builder\Namespace_::class],
            ['class',       Builder\Class_::class],
            ['interface',   Builder\Interface_::class],
            ['trait',       Builder\Trait_::class],
            ['enum',        Builder\Enum_::class],
            ['method',      Builder\Method::class],
            ['function',    Builder\Function_::class],
            ['property',    Builder\Property::class],
            ['param',       Builder\Param::class],
            ['use',         Builder\Use_::class],
            ['useFunction', Builder\Use_::class],
            ['useConst',    Builder\Use_::class],
            ['enumCase',    Builder\EnumCase::class],
        ];
    }

    public function testFactoryClassConst(): void {
        $factory = new BuilderFactory();
        $this->assertInstanceOf(Builder\ClassConst::class, $factory->classConst('TEST', 1));
    }

    public function testAttribute(): void {
        $factory = new BuilderFactory();
        $this->assertEquals(
            new Attribute(new Name('AttributeName'), [new Arg(
                new String_('bar'), false, false, [], new Identifier('foo')
            )]),
            $factory->attribute('AttributeName', ['foo' => 'bar'])
        );
    }

    public function testVal(): void {
        // This method is a wrapper around BuilderHelpers::normalizeValue(),
        // which is already tested elsewhere
        $factory = new BuilderFactory();
        $this->assertEquals(
            new String_("foo"),
            $factory->val("foo")
        );
    }

    public function testConcat(): void {
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

    public function testConcatOneError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected at least two expressions');
        (new BuilderFactory())->concat("a");
    }

    public function testConcatInvalidExpr(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected string or Expr');
        (new BuilderFactory())->concat("a", 42);
    }

    public function testArgs(): void {
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

    public function testNamedArgs(): void {
        $factory = new BuilderFactory();
        $this->assertEquals(
            [
                new Arg(new String_('foo')),
                new Arg(new String_('baz'), false, false, [], new Identifier('bar')),
            ],
            $factory->args(['foo', 'bar' => 'baz'])
        );
    }

    public function testCalls(): void {
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
                [new Arg(new Int_(42))]
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

    public function testConstFetches(): void {
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
        $this->assertEquals(
            new Expr\ClassConstFetch(new Name('Foo'), new Expr\Variable('foo')),
            $factory->classConstFetch('Foo', $factory->var('foo'))
        );
    }

    public function testVar(): void {
        $factory = new BuilderFactory();
        $this->assertEquals(
            new Expr\Variable("foo"),
            $factory->var("foo")
        );
        $this->assertEquals(
            new Expr\Variable(new Expr\Variable("foo")),
            $factory->var($factory->var("foo"))
        );
    }

    public function testPropertyFetch(): void {
        $f = new BuilderFactory();
        $this->assertEquals(
            new Expr\PropertyFetch(new Expr\Variable('foo'), 'bar'),
            $f->propertyFetch($f->var('foo'), 'bar')
        );
        $this->assertEquals(
            new Expr\PropertyFetch(new Expr\Variable('foo'), 'bar'),
            $f->propertyFetch($f->var('foo'), new Identifier('bar'))
        );
        $this->assertEquals(
            new Expr\PropertyFetch(new Expr\Variable('foo'), new Expr\Variable('bar')),
            $f->propertyFetch($f->var('foo'), $f->var('bar'))
        );
    }

    public function testInvalidIdentifier(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected string or instance of Node\Identifier');
        (new BuilderFactory())->classConstFetch('Foo', new Name('foo'));
    }

    public function testInvalidIdentifierOrExpr(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Expected string or instance of Node\Identifier or Node\Expr');
        (new BuilderFactory())->staticCall('Foo', new Name('bar'));
    }

    public function testInvalidNameOrExpr(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name must be a string or an instance of Node\Name or Node\Expr');
        (new BuilderFactory())->funcCall(new Node\Stmt\Return_());
    }

    public function testInvalidVar(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Variable name must be string or Expr');
        (new BuilderFactory())->var(new Node\Stmt\Return_());
    }

    public function testIntegration(): void {
        $factory = new BuilderFactory();
        $node = $factory->namespace('Name\Space')
            ->addStmt($factory->use('Foo\Bar\SomeOtherClass'))
            ->addStmt($factory->use('Foo\Bar')->as('A'))
            ->addStmt($factory->useFunction('strlen'))
            ->addStmt($factory->useConst('PHP_VERSION'))
            ->addStmt($factory
                ->class('SomeClass')
                ->extend('SomeOtherClass')
                ->implement('A\Few', '\Interfaces')
                ->addAttribute($factory->attribute('ClassAttribute', ['repository' => 'fqcn']))
                ->makeAbstract()

                ->addStmt($factory->useTrait('FirstTrait'))

                ->addStmt($factory->useTrait('SecondTrait', 'ThirdTrait')
                    ->and('AnotherTrait')
                    ->with($factory->traitUseAdaptation('foo')->as('bar'))
                    ->with($factory->traitUseAdaptation('AnotherTrait', 'baz')->as('test'))
                    ->with($factory->traitUseAdaptation('AnotherTrait', 'func')->insteadof('SecondTrait')))

                ->addStmt($factory->method('firstMethod')
                    ->addAttribute($factory->attribute('Route', ['/index', 'name' => 'homepage']))
                )

                ->addStmt($factory->method('someMethod')
                    ->makePublic()
                    ->makeAbstract()
                    ->addParam($factory->param('someParam')->setType('SomeClass'))
                    ->setDocComment('/**
                                      * This method does something.
                                      *
                                      * @param SomeClass And takes a parameter
                                      */'))

                ->addStmt($factory->method('anotherMethod')
                    ->makeProtected()
                    ->addParam($factory->param('someParam')
                        ->setDefault('test')
                        ->addAttribute($factory->attribute('TaggedIterator', ['app.handlers']))
                    )
                    ->addStmt(new Expr\Print_(new Expr\Variable('someParam'))))

                ->addStmt($factory->property('someProperty')->makeProtected())
                ->addStmt($factory->property('anotherProperty')
                    ->makePrivate()
                    ->setDefault([1, 2, 3]))
                ->addStmt($factory->property('integerProperty')
                    ->setType('int')
                    ->addAttribute($factory->attribute('Column', ['options' => ['unsigned' => true]]))
                    ->setDefault(1))
                ->addStmt($factory->classConst('CONST_WITH_ATTRIBUTE', 1)
                    ->makePublic()
                    ->addAttribute($factory->attribute('ConstAttribute'))
                )

                ->addStmt($factory->classConst("FIRST_CLASS_CONST", 1)
                    ->addConst("SECOND_CLASS_CONST", 2)
                    ->makePrivate()))
            ->getNode()
        ;

        $expected = <<<'EOC'
<?php

namespace Name\Space;

use Foo\Bar\SomeOtherClass;
use Foo\Bar as A;
use function strlen;
use const PHP_VERSION;
#[ClassAttribute(repository: 'fqcn')]
abstract class SomeClass extends SomeOtherClass implements A\Few, \Interfaces
{
    use FirstTrait;
    use SecondTrait, ThirdTrait, AnotherTrait {
        foo as bar;
        AnotherTrait::baz as test;
        AnotherTrait::func insteadof SecondTrait;
    }
    #[ConstAttribute]
    public const CONST_WITH_ATTRIBUTE = 1;
    private const FIRST_CLASS_CONST = 1, SECOND_CLASS_CONST = 2;
    protected $someProperty;
    private $anotherProperty = [1, 2, 3];
    #[Column(options: ['unsigned' => true])]
    public int $integerProperty = 1;
    #[Route('/index', name: 'homepage')]
    function firstMethod()
    {
    }
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    abstract public function someMethod(SomeClass $someParam);
    protected function anotherMethod(
        #[TaggedIterator('app.handlers')]
        $someParam = 'test'
    )
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
