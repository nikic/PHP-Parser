<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

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

    public function provideTestDefaultValues(): \Iterator
    {
        yield [
            null,
            new Expr\ConstFetch(new Node\Name('null'))
        ];
        yield [
            true,
            new Expr\ConstFetch(new Node\Name('true'))
        ];
        yield [
            false,
            new Expr\ConstFetch(new Node\Name('false'))
        ];
        yield [
            31415,
            new Scalar\LNumber(31415)
        ];
        yield [
            3.1415,
            new Scalar\DNumber(3.1415)
        ];
        yield [
            'Hallo World',
            new Scalar\String_('Hallo World')
        ];
        yield [
            [1, 2, 3],
            new Expr\Array_([
                new Expr\ArrayItem(new Scalar\LNumber(1)),
                new Expr\ArrayItem(new Scalar\LNumber(2)),
                new Expr\ArrayItem(new Scalar\LNumber(3)),
            ])
        ];
        yield [
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
        ];
        yield [
            new Scalar\MagicConst\Dir,
            new Scalar\MagicConst\Dir
        ];
    }

    /**
     * @dataProvider provideTestTypes
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

    public function provideTestTypes(): \Iterator
    {
        yield ['array', new Node\Identifier('array')];
        yield ['callable', new Node\Identifier('callable')];
        yield ['bool', new Node\Identifier('bool')];
        yield ['int', new Node\Identifier('int')];
        yield ['float', new Node\Identifier('float')];
        yield ['string', new Node\Identifier('string')];
        yield ['iterable', new Node\Identifier('iterable')];
        yield ['object', new Node\Identifier('object')];
        yield ['Array', new Node\Identifier('array')];
        yield ['CALLABLE', new Node\Identifier('callable')];
        yield ['Some\Class', new Node\Name('Some\Class')];
        yield ['\Foo', new Node\Name\FullyQualified('Foo')];
        yield ['self', new Node\Name('self')];
        yield ['?array', new Node\NullableType(new Node\Identifier('array'))];
        yield ['?Some\Class', new Node\NullableType(new Node\Name('Some\Class'))];
        yield [new Node\Name('Some\Class'), new Node\Name('Some\Class')];
        yield [
            new Node\NullableType(new Node\Identifier('int')),
            new Node\NullableType(new Node\Identifier('int'))
        ];
        yield [
            new Node\NullableType(new Node\Name('Some\Class')),
            new Node\NullableType(new Node\Name('Some\Class'))
        ];
    }

    public function testVoidTypeError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Parameter type cannot be void');
        $this->createParamBuilder('test')->setType('void');
    }

    public function testInvalidTypeError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type must be a string, or an instance of Name, Identifier or NullableType');
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
}
