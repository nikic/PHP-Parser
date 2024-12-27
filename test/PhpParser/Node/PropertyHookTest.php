<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class PropertyHookTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier): void {
        $node = new PropertyHook(
            'get',
            null,
            [
                'flags' => constant(Modifiers::class . '::' . strtoupper($modifier)),
            ]
        );

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers(): void {
        $node = new PropertyHook('get', null);

        $this->assertFalse($node->isFinal());
    }

    public static function provideModifiers() {
        return [
            ['final'],
        ];
    }

    public function testGetStmts(): void {
        $expr = new Variable('test');
        $get = new PropertyHook('get', $expr);
        $this->assertEquals([new Return_($expr)], $get->getStmts());

        $set = new PropertyHook('set', $expr, [], ['propertyName' => 'abc']);
        $this->assertEquals([
            new Expression(new Assign(new PropertyFetch(new Variable('this'), 'abc'), $expr))
        ], $set->getStmts());
    }

    public function testGetStmtsSetHookFromParser(): void {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $prettyPrinter = new Standard();
        $stmts = $parser->parse(<<<'CODE'
        <?php
        class Test {
            public $prop1 { set => 123; }

            public function __construct(public $prop2 { set => 456; }) {}
        }
        CODE);

        $hook1 = $stmts[0]->stmts[0]->hooks[0];
        $this->assertEquals('$this->prop1 = 123;', $prettyPrinter->prettyPrint($hook1->getStmts()));

        $hook2 = $stmts[0]->stmts[1]->params[0]->hooks[0];
        $this->assertEquals('$this->prop2 = 456;', $prettyPrinter->prettyPrint($hook2->getStmts()));
    }

    public function testGetStmtsUnknownHook(): void {
        $expr = new Variable('test');
        $hook = new PropertyHook('foobar', $expr);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unknown property hook "foobar"');
        $hook->getStmts();
    }

    public function testGetStmtsSetHookWithoutPropertyName(): void {
        $expr = new Variable('test');
        $set = new PropertyHook('set', $expr);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Can only use getStmts() on a "set" hook if the "propertyName" attribute is set');
        $set->getStmts();
    }
}
