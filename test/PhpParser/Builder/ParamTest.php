<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Int_;

class ParamTest extends \PHPUnit\Framework\TestCase {
    public function createParamBuilder($name) {
        return new Param($name);
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode): void {
        $node = $this->createParamBuilder('test')
            ->setDefault($value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->default);
    }

    public static function provideTestDefaultValues() {
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
                new Scalar\Int_(31415)
            ],
            [
                3.1415,
                new Scalar\Float_(3.1415)
            ],
            [
                'Hallo World',
                new Scalar\String_('Hallo World')
            ],
            [
                [1, 2, 3],
                new Expr\Array_([
                    new Node\ArrayItem(new Scalar\Int_(1)),
                    new Node\ArrayItem(new Scalar\Int_(2)),
                    new Node\ArrayItem(new Scalar\Int_(3)),
                ])
            ],
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                new Expr\Array_([
                    new Node\ArrayItem(
                        new Scalar\String_('bar'),
                        new Scalar\String_('foo')
                    ),
                    new Node\ArrayItem(
                        new Scalar\String_('foo'),
                        new Scalar\String_('bar')
                    ),
                ])
            ],
            [
                new Scalar\MagicConst\Dir(),
                new Scalar\MagicConst\Dir()
            ]
        ];
    }

    /**
     * @dataProvider provideTestTypes
     * @dataProvider provideTestNullableTypes
     * @dataProvider provideTestUnionTypes
     */
    public function testTypes($typeHint, $expectedType): void {
        $node = $this->createParamBuilder('test')
            ->setType($typeHint)
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

    public static function provideTestTypes() {
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

    public static function provideTestNullableTypes() {
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

    public static function provideTestUnionTypes() {
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

    public function testVoidTypeError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Parameter type cannot be void');
        $this->createParamBuilder('test')->setType('void');
    }

    public function testInvalidTypeError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type must be a string, or an instance of Name, Identifier or ComplexType');
        $this->createParamBuilder('test')->setType(new \stdClass());
    }

    public function testByRef(): void {
        $node = $this->createParamBuilder('test')
            ->makeByRef()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, true),
            $node
        );
    }

    public function testVariadic(): void {
        $node = $this->createParamBuilder('test')
            ->makeVariadic()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, true),
            $node
        );
    }

    public function testMakePublic(): void {
        $node = $this->createParamBuilder('test')
            ->makePublic()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::PUBLIC),
            $node
        );
    }

    public function testMakeProtected(): void {
        $node = $this->createParamBuilder('test')
            ->makeProtected()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::PROTECTED),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->makeProtectedSet()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::PROTECTED_SET),
            $node
        );
    }

    public function testMakePrivate(): void {
        $node = $this->createParamBuilder('test')
            ->makePrivate()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::PRIVATE),
            $node
        );

        $node = $this->createParamBuilder('test')
            ->makePrivateSet()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::PRIVATE_SET),
            $node
        );
    }

    public function testMakeReadonly(): void {
        $node = $this->createParamBuilder('test')
            ->makeReadonly()
            ->getNode()
        ;

        $this->assertEquals(
            new Node\Param(new Expr\Variable('test'), null, null, false, false, [], Modifiers::READONLY),
            $node
        );
    }

    public function testAddAttribute(): void {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new Int_(1), false, false, [], new Identifier('name'))]
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
