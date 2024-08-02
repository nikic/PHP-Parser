<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Modifiers;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;

class ClassConstTest extends \PHPUnit\Framework\TestCase {
    public function createClassConstBuilder($name, $value) {
        return new ClassConst($name, $value);
    }

    public function testModifiers(): void {
        $node = $this->createClassConstBuilder("TEST", 1)
            ->makePrivate()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new Int_(1))
                ],
                Modifiers::PRIVATE
            ),
            $node
        );

        $node = $this->createClassConstBuilder("TEST", 1)
            ->makeProtected()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new Int_(1))
                ],
                Modifiers::PROTECTED
            ),
            $node
        );

        $node = $this->createClassConstBuilder("TEST", 1)
            ->makePublic()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new Int_(1))
                ],
                Modifiers::PUBLIC
            ),
            $node
        );

        $node = $this->createClassConstBuilder("TEST", 1)
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new Int_(1))
                ],
                Modifiers::FINAL
            ),
            $node
        );
    }

    public function testDocComment(): void {
        $node = $this->createClassConstBuilder('TEST', 1)
            ->setDocComment('/** Test */')
            ->makePublic()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new Int_(1))
                ],
                Modifiers::PUBLIC,
                [
                    'comments' => [new Comment\Doc('/** Test */')]
                ]
            ),
            $node
        );
    }

    public function testAddConst(): void {
        $node = $this->createClassConstBuilder('FIRST_TEST', 1)
            ->addConst("SECOND_TEST", 2)
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("FIRST_TEST", new Int_(1)),
                    new Const_("SECOND_TEST", new Int_(2))
                ]
            ),
            $node
        );
    }

    public function testAddAttribute(): void {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new Int_(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $node = $this->createClassConstBuilder('ATTR_GROUP', 1)
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("ATTR_GROUP", new Int_(1))
                ],
                0,
                [],
                [$attributeGroup]
            ),
            $node
        );
    }

    public function testType(): void {
        $node = $this->createClassConstBuilder('TYPE', 1)
            ->setType('int')
            ->getNode();
        $this->assertEquals(
            new Stmt\ClassConst(
                [new Const_('TYPE', new Int_(1))],
                0, [], [], new Identifier('int')),
            $node
        );
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testValues($value, $expectedValueNode): void {
        $node = $this->createClassConstBuilder('TEST', $value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->consts[0]->value);
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
