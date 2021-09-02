<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\LNumber;

class ParamTest extends \PHPUnit\Framework\TestCase
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
        return [
            [
                null,
                new Expr\ConstFetch(new Node\Name('null'))
            ],
            [
                true,
                new Expr\ConstFetch(new Node\Name('true'))
            ],
            [
                false,
                new Expr\ConstFetch(new Node\Name('false'))
            ],
            [
                31415,
                new Scalar\LNumber(31415)
            ],
            [
                3.1415,
                new Scalar\DNumber(3.1415)
            ],
            [
                'Hallo World',
                new Scalar\String_('Hallo World')
            ],
            [
                [1, 2, 3],
                new Expr\Array_([
                    new Expr\ArrayItem(new Scalar\LNumber(1)),
                    new Expr\ArrayItem(new Scalar\LNumber(2)),
                    new Expr\ArrayItem(new Scalar\LNumber(3)),
                ])
            ],
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                new Expr\Array_([
                    new Expr\ArrayItem(
                        new Scalar\String_('bar'),
                        new Scalar\String_('foo')
                    ),
                    new Expr\ArrayItem(
                        new Scalar\String_('foo'),
                        new Scalar\String_('bar')
                    ),
                ])
            ],
            [
                new Scalar\MagicConst\Dir,
                new Scalar\MagicConst\Dir
            ]
        ];
    }

    /**
     * @dataProvider provideTestTypes
     * @dataProvider provideTestNullableTypes
     * @dataProvider provideTestUnionTypes
     */
    public function testTypes($typeHint, $expectedType) {
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

    public function provideTestTypes() {
        return [
            ['array', new Node\Identifier('array')],
            ['callable', new Node\Identifier('callable')],
            ['bool', new Node\Identifier('bool')],
            ['int', new Node\Identifier('int')],
            ['float', new Node\Identifier('float')],
            ['string', new Node\Identifier('string')],
            ['iterable', new Node\Identifier('iterable')],
            ['object', new Node\Identifier('object')],
            ['Array', new Node\Identifier('array')],
            ['CALLABLE', new Node\Identifier('callable')],
            ['mixed', new Node\Identifier('mixed')],
            ['Some\Class', new Node\Name('Some\Class')],
            ['\Foo', new Node\Name\FullyQualified('Foo')],
            ['self', new Node\Name('self')],
            [new Node\Name('Some\Class'), new Node\Name('Some\Class')],
        ];
    }

    public function provideTestNullableTypes() {
        return [
            ['?array', new Node\NullableType(new Node\Identifier('array'))],
            ['?Some\Class', new Node\NullableType(new Node\Name('Some\Class'))],
            [
                new Node\NullableType(new Node\Identifier('int')),
                new Node\NullableType(new Node\Identifier('int'))
            ],
            [
                new Node\NullableType(new Node\Name('Some\Class')),
                new Node\NullableType(new Node\Name('Some\Class'))
            ],
        ];
    }

    public function provideTestUnionTypes() {
        return [
            [
                new Node\UnionType([
                    new Node\Name('Some\Class'),
                    new Node\Identifier('array'),
                ]),
                new Node\UnionType([
                    new Node\Name('Some\Class'),
                    new Node\Identifier('array'),
                ]),
            ],
            [
                new Node\UnionType([
                    new Node\Identifier('self'),
                    new Node\Identifier('array'),
                    new Node\Name\FullyQualified('Foo')
                ]),
                new Node\UnionType([
                    new Node\Identifier('self'),
                    new Node\Identifier('array'),
                    new Node\Name\FullyQualified('Foo')
                ]),
            ],
        ];
    }

    public function testVoidTypeError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Parameter type cannot be void');
        $this->createParamBuilder('test')->setType('void');
    }

    public function testInvalidTypeError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type must be a string, or an instance of Name, Identifier or ComplexType');
        $this->createParamBuilder('test')->setType(new \stdClass);
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

    public function testAddAttribute() {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new LNumber(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $node = $this->createParamBuilder('attributeGroup')
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Node\Param(new Expr\Variable('attributeGroup'), null, null, false, false, [], 0, [$attributeGroup]),
            $node
        );
    }
}
