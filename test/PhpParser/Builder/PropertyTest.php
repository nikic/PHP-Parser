<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Modifiers;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;

class PropertyTest extends \PHPUnit\Framework\TestCase {
    public function createPropertyBuilder($name) {
        return new Property($name);
    }

    public function testModifiers(): void {
        $node = $this->createPropertyBuilder('test')
            ->makePrivate()
            ->makeStatic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Modifiers::PRIVATE | Modifiers::STATIC,
                [
                    new \PhpParser\Node\PropertyItem('test')
                ]
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makeProtected()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Modifiers::PROTECTED,
                [
                    new \PhpParser\Node\PropertyItem('test')
                ]
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makePublic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Modifiers::PUBLIC,
                [
                    new \PhpParser\Node\PropertyItem('test')
                ]
            ),
            $node
        );

        $node = $this->createPropertyBuilder('test')
            ->makeReadonly()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Modifiers::READONLY,
                [
                    new \PhpParser\Node\PropertyItem('test')
                ]
            ),
            $node
        );
    }

    public function testDocComment(): void {
        $node = $this->createPropertyBuilder('test')
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(new Stmt\Property(
            Modifiers::PUBLIC,
            [
                new \PhpParser\Node\PropertyItem('test')
            ],
            [
                'comments' => [new Comment\Doc('/** Test */')]
            ]
        ), $node);
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testDefaultValues($value, $expectedValueNode): void {
        $node = $this->createPropertyBuilder('test')
            ->setDefault($value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->props[0]->default);
    }

    public function testAddAttribute(): void {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new Int_(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $node = $this->createPropertyBuilder('test')
            ->addAttribute($attributeGroup)
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Property(
                Modifiers::PUBLIC,
                [
                    new \PhpParser\Node\PropertyItem('test')
                ],
                [],
                null,
                [$attributeGroup]
            ),
            $node
        );
    }

    public static function provideTestDefaultValues() {
        return [
            [
                null,
                new Expr\ConstFetch(new Name('null'))
            ],
            [
                true,
                new Expr\ConstFetch(new Name('true'))
            ],
            [
                false,
                new Expr\ConstFetch(new Name('false'))
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
                    new \PhpParser\Node\ArrayItem(new Scalar\Int_(1)),
                    new \PhpParser\Node\ArrayItem(new Scalar\Int_(2)),
                    new \PhpParser\Node\ArrayItem(new Scalar\Int_(3)),
                ])
            ],
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                new Expr\Array_([
                    new \PhpParser\Node\ArrayItem(
                        new Scalar\String_('bar'),
                        new Scalar\String_('foo')
                    ),
                    new \PhpParser\Node\ArrayItem(
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
}
