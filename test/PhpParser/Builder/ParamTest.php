<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PHPUnit\Framework\TestCase;

class ParamTest extends TestCase
{
    public function createParamBuilder($name) {
        return new Param($name);
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode) {
        $node = $this->createParamBuilder('test')
            ->setDefault($value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->default);
    }

    public function provideTestDefaultValues() {
        return array(
            array(
                null,
                new Expr\ConstFetch(new Node\Name('null'))
            ),
            array(
                true,
                new Expr\ConstFetch(new Node\Name('true'))
            ),
            array(
                false,
                new Expr\ConstFetch(new Node\Name('false'))
            ),
            array(
                31415,
                new Scalar\LNumber(31415)
            ),
            array(
                3.1415,
                new Scalar\DNumber(3.1415)
            ),
            array(
                'Hallo World',
                new Scalar\String_('Hallo World')
            ),
            array(
                array(1, 2, 3),
                new Expr\Array_(array(
                    new Expr\ArrayItem(new Scalar\LNumber(1)),
                    new Expr\ArrayItem(new Scalar\LNumber(2)),
                    new Expr\ArrayItem(new Scalar\LNumber(3)),
                ))
            ),
            array(
                array('foo' => 'bar', 'bar' => 'foo'),
                new Expr\Array_(array(
                    new Expr\ArrayItem(
                        new Scalar\String_('bar'),
                        new Scalar\String_('foo')
                    ),
                    new Expr\ArrayItem(
                        new Scalar\String_('foo'),
                        new Scalar\String_('bar')
                    ),
                ))
            ),
            array(
                new Scalar\MagicConst\Dir,
                new Scalar\MagicConst\Dir
            )
        );
    }

    /**
     * @dataProvider provideTestTypeHints
     */
    public function testTypeHints($typeHint, $expectedType) {
        $node = $this->createParamBuilder('test')
            ->setTypeHint($typeHint)
            ->getNode()
        ;
        $type = $node->type;

        /* Manually implement comparison to avoid __toString stupidity */
        if ($expectedType instanceof Node\NullableType) {
            $this->assertInstanceOf(get_class($expectedType), $type);
            $expectedType = $expectedType->type;
            $type = $type->type;
        }

        $this->assertInstanceOf(get_class($expectedType), $type);
        $this->assertEquals($expectedType, $type);
    }

    public function provideTestTypeHints() {
        return array(
            array('array', new Node\Identifier('array')),
            array('callable', new Node\Identifier('callable')),
            array('bool', new Node\Identifier('bool')),
            array('int', new Node\Identifier('int')),
            array('float', new Node\Identifier('float')),
            array('string', new Node\Identifier('string')),
            array('iterable', new Node\Identifier('iterable')),
            array('Array', new Node\Identifier('array')),
            array('CALLABLE', new Node\Identifier('callable')),
            array('Some\Class', new Node\Name('Some\Class')),
            array('\Foo', new Node\Name\FullyQualified('Foo')),
            array('self', new Node\Name('self')),
            array('?array', new Node\NullableType(new Node\Identifier('array'))),
            array('?Some\Class', new Node\NullableType(new Node\Name('Some\Class'))),
            array(new Node\Name('Some\Class'), new Node\Name('Some\Class')),
            array(
                new Node\NullableType(new Node\Identifier('int')),
                new Node\NullableType(new Node\Identifier('int'))
            ),
            array(
                new Node\NullableType(new Node\Name('Some\Class')),
                new Node\NullableType(new Node\Name('Some\Class'))
            ),
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Parameter type cannot be void
     */
    public function testVoidTypeError() {
        $this->createParamBuilder('test')->setTypeHint('void');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Type must be a string, or an instance of Name, Identifier or NullableType
     */
    public function testInvalidTypeError() {
        $this->createParamBuilder('test')->setTypeHint(new \stdClass);
    }

    public function testByRef() {
        $node = $this->createParamBuilder('test')
            ->makeByRef()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, true),
            $node
        );
    }

    public function testVariadic() {
        $node = $this->createParamBuilder('test')
            ->makeVariadic()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, true),
            $node
        );
    }
}
