<?php

namespace PhpParser\Builder;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class PropertyTest extends \PHPUnit_Framework_TestCase
{
    public function createPropertyBuilder($name) {
        return new Property($name);
    }

    public function testModifiers() {
        $node = $this->createPropertyBuilder('test')
            ->makePrivate()
            ->makeStatic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Stmt\Class_::MODIFIER_PRIVATE
              | Stmt\Class_::MODIFIER_STATIC,
                array(
                    new Stmt\PropertyProperty('test')
                )
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makeProtected()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Stmt\Class_::MODIFIER_PROTECTED,
                array(
                    new Stmt\PropertyProperty('test')
                )
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makePublic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Stmt\Class_::MODIFIER_PUBLIC,
                array(
                    new Stmt\PropertyProperty('test')
                )
            ),
            $node
        );
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode) {
        $node = $this->createPropertyBuilder('test')
            ->setDefault($value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->props[0]->default);
    }

    public function provideTestDefaultValues() {
        return array(
            array(
                null,
                new Expr\ConstFetch(new Name('null'))
            ),
            array(
                true,
                new Expr\ConstFetch(new Name('true'))
            ),
            array(
                false,
                new Expr\ConstFetch(new Name('false'))
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
                new Scalar\String('Hallo World')
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
                        new Scalar\String('bar'),
                        new Scalar\String('foo')
                    ),
                    new Expr\ArrayItem(
                        new Scalar\String('foo'),
                        new Scalar\String('bar')
                    ),
                ))
            ),
            array(
                new Scalar\MagicConst\Dir,
                new Scalar\MagicConst\Dir
            )
        );
    }
}