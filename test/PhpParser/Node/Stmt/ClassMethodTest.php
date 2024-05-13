<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;

class ClassMethodTest extends \PHPUnit\Framework\TestCase {
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier): void {
        $node = new ClassMethod('foo', [
            'type' => constant(Modifiers::class . '::' . strtoupper($modifier))
        ]);

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers(): void {
        $node = new ClassMethod('foo', ['type' => 0]);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isAbstract());
        $this->assertFalse($node->isFinal());
        $this->assertFalse($node->isStatic());
        $this->assertFalse($node->isMagic());
    }

    public static function provideModifiers() {
        return [
            ['public'],
            ['protected'],
            ['private'],
            ['abstract'],
            ['final'],
            ['static'],
        ];
    }

    /**
     * Checks that implicit public modifier detection for method is working
     *
     * @dataProvider implicitPublicModifiers
     *
     * @param string $modifier Node type modifier
     */
    public function testImplicitPublic(string $modifier): void {
        $node = new ClassMethod('foo', [
            'type' => constant(Modifiers::class . '::' . strtoupper($modifier))
        ]);

        $this->assertTrue($node->isPublic(), 'Node should be implicitly public');
    }

    public static function implicitPublicModifiers() {
        return [
            ['abstract'],
            ['final'],
            ['static'],
        ];
    }

    /**
     * @dataProvider provideMagics
     *
     * @param string $name Node name
     */
    public function testMagic(string $name): void {
        $node = new ClassMethod($name);
        $this->assertTrue($node->isMagic(), 'Method should be magic');
    }

    public static function provideMagics() {
        return [
             ['__construct'],
             ['__DESTRUCT'],
             ['__caLL'],
             ['__callstatic'],
             ['__get'],
             ['__set'],
             ['__isset'],
             ['__unset'],
             ['__sleep'],
             ['__wakeup'],
             ['__tostring'],
             ['__set_state'],
             ['__clone'],
             ['__invoke'],
             ['__debuginfo'],
        ];
    }

    public function testFunctionLike(): void {
        $param = new Param(new Variable('a'));
        $type = new Name('Foo');
        $return = new Return_(new Variable('a'));
        $method = new ClassMethod('test', [
            'byRef' => false,
            'params' => [$param],
            'returnType' => $type,
            'stmts' => [$return],
        ]);

        $this->assertFalse($method->returnsByRef());
        $this->assertSame([$param], $method->getParams());
        $this->assertSame($type, $method->getReturnType());
        $this->assertSame([$return], $method->getStmts());

        $method = new ClassMethod('test', [
            'byRef' => true,
            'stmts' => null,
        ]);

        $this->assertTrue($method->returnsByRef());
        $this->assertNull($method->getStmts());
    }
}
