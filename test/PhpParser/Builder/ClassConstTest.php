<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;

class ClassConstTest extends \PHPUnit\Framework\TestCase
{
    public function createClassConstBuilder($name, $value) {
        return new ClassConst($name, $value);
    }

    public function testModifiers() {
        $node = $this->createClassConstBuilder("TEST", 1)
            ->makePrivate()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new LNumber(1))
                ],
                Stmt\Class_::MODIFIER_PRIVATE
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
                    new Const_("TEST", new LNumber(1) )
                ],
                Stmt\Class_::MODIFIER_PROTECTED
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
                    new Const_("TEST", new LNumber(1) )
                ],
                Stmt\Class_::MODIFIER_PUBLIC
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
                    new Const_("TEST", new LNumber(1) )
                ],
                Stmt\Class_::MODIFIER_FINAL
            ),
            $node
        );
    }

    public function testDocComment() {
        $node = $this->createClassConstBuilder('TEST',1)
            ->setDocComment('/** Test */')
            ->makePublic()
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("TEST", new LNumber(1) )
                ],
                Stmt\Class_::MODIFIER_PUBLIC,
                [
                    'comments' => [new Comment\Doc('/** Test */')]
                ]
            ),
            $node
        );
    }

    public function testAddConst() {
        $node = $this->createClassConstBuilder('FIRST_TEST',1)
            ->addConst("SECOND_TEST",2)
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("FIRST_TEST", new LNumber(1)),
                    new Const_("SECOND_TEST", new LNumber(2))
                ]
            ),
            $node
        );
    }

    public function testAddAttribute() {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new LNumber(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $node = $this->createClassConstBuilder('ATTR_GROUP', 1)
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Stmt\ClassConst(
                [
                    new Const_("ATTR_GROUP", new LNumber(1) )
                ],
                0,
                [],
                [$attributeGroup]
            ),
            $node
        );
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testValues($value, $expectedValueNode) {
        $node = $this->createClassConstBuilder('TEST', $value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->consts[0]->value);
    }

    public function provideTestDefaultValues() {
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
}
