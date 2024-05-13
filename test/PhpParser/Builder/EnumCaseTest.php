<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;

class EnumCaseTest extends \PHPUnit\Framework\TestCase {
    public function createEnumCaseBuilder($name) {
        return new EnumCase($name);
    }

    public function testDocComment(): void {
        $node = $this->createEnumCaseBuilder('TEST')
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(
            new Stmt\EnumCase(
                "TEST",
                null,
                [],
                [
                    'comments' => [new Comment\Doc('/** Test */')]
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

        $node = $this->createEnumCaseBuilder('ATTR_GROUP')
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Stmt\EnumCase(
                "ATTR_GROUP",
                null,
                [$attributeGroup]
            ),
            $node
        );
    }

    /**
     * @dataProvider provideTestDefaultValues
     */
    public function testValues($value, $expectedValueNode): void {
        $node = $this->createEnumCaseBuilder('TEST')
            ->setValue($value)
            ->getNode()
        ;

        $this->assertEquals($expectedValueNode, $node->expr);
    }

    public static function provideTestDefaultValues() {
        return [
            [
                31415,
                new Scalar\Int_(31415)
            ],
            [
                'Hallo World',
                new Scalar\String_('Hallo World')
            ],
        ];
    }
}
