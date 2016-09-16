<?php

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class ParamTest extends \PHPUnit_Framework_TestCase
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

        if ($expectedType instanceof Node\Name) {
            $this->assertInstanceOf(get_class($expectedType), $type);
            $this->assertEquals($expectedType, $type);
        } else {
            $this->assertSame($expectedType, $type);
        }
    }

    public function provideTestTypeHints() {
        return array(
            array('array', 'array'),
            array('callable', 'callable'),
            array('bool', 'bool'),
            array('int', 'int'),
            array('float', 'float'),
            array('string', 'string'),
            array('iterable', 'iterable'),
            array('Array', 'array'),
            array('CALLABLE', 'callable'),
            array('Some\Class', new Node\Name('Some\Class')),
            array('\Foo', new Node\Name\FullyQualified('Foo')),
            array('self', new Node\Name('self')),
            array('?array', new Node\NullableType('array')),
            array('?Some\Class', new Node\NullableType(new Node\Name('Some\Class'))),
            array(new Node\Name('Some\Class'), new Node\Name('Some\Class')),
            array(new Node\NullableType('int'), new Node\NullableType('int')),
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
     * @expectedExceptionMessage Type must be a string, or an instance of Name or NullableType
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
            new Node\Param('test', null, null, true),
            $node
        );
    }
}
