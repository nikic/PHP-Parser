<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;

class ClassMethodTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideModifiers
     */
    public function testModifiers($modifier) {
        $node = new ClassMethod('foo', [
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_' . strtoupper($modifier))
        ]);

        $this->assertTrue($node->{'is' . $modifier}());
    }

    public function testNoModifiers() {
        $node = new ClassMethod('foo', ['type' => 0]);

        $this->assertTrue($node->isPublic());
        $this->assertFalse($node->isProtected());
        $this->assertFalse($node->isPrivate());
        $this->assertFalse($node->isAbstract());
        $this->assertFalse($node->isFinal());
        $this->assertFalse($node->isStatic());
        $this->assertFalse($node->isMagic());
    }

    public function provideModifiers(): \Iterator
    {
        yield ['public'];
        yield ['protected'];
        yield ['private'];
        yield ['abstract'];
        yield ['final'];
        yield ['static'];
    }

    /**
     * Checks that implicit public modifier detection for method is working
     *
     * @dataProvider implicitPublicModifiers
     *
     * @param string $modifier Node type modifier
     */
    public function testImplicitPublic(string $modifier)
    {
        $node = new ClassMethod('foo', [
            'type' => constant('PhpParser\Node\Stmt\Class_::MODIFIER_' . strtoupper($modifier))
        ]);

        $this->assertTrue($node->isPublic(), 'Node should be implicitly public');
    }

    public function implicitPublicModifiers(): \Iterator
    {
        yield ['abstract'];
        yield ['final'];
        yield ['static'];
    }

    /**
     * @dataProvider provideMagics
     *
     * @param string $name Node name
     */
    public function testMagic(string $name) {
        $node = new ClassMethod($name);
        $this->assertTrue($node->isMagic(), 'Method should be magic');
    }

    public function provideMagics(): \Iterator
    {
        yield ['__construct'];
        yield ['__DESTRUCT'];
        yield ['__caLL'];
        yield ['__callstatic'];
        yield ['__get'];
        yield ['__set'];
        yield ['__isset'];
        yield ['__unset'];
        yield ['__sleep'];
        yield ['__wakeup'];
        yield ['__tostring'];
        yield ['__set_state'];
        yield ['__clone'];
        yield ['__invoke'];
        yield ['__debuginfo'];
    }

    public function testFunctionLike() {
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
