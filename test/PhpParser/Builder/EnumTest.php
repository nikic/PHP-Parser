<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;

class EnumTest extends \PHPUnit\Framework\TestCase
{
    protected function createEnumBuilder($class) {
        return new Enum_($class);
    }

    public function testImplements() {
        $node = $this->createEnumBuilder('SomeEnum')
            ->implement('Namespaced\SomeInterface', new Name('OtherInterface'))
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Enum_('SomeEnum', [
                'implements' => [
                    new Name('Namespaced\SomeInterface'),
                    new Name('OtherInterface'),
                ],
            ]),
            $node
        );
    }

    public function testSetScalarType() {
        $node = $this->createEnumBuilder('Test')
            ->setScalarType('int')
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Enum_('Test', [
                'scalarType' => new Identifier('int'),
            ]),
            $node
        );
    }

    public function testStatementOrder() {
        $method = new Stmt\ClassMethod('testMethod');
        $enumCase = new Stmt\EnumCase(
            'TEST_ENUM_CASE'
        );
        $const = new Stmt\ClassConst([
            new Node\Const_('TEST_CONST', new Node\Scalar\String_('ABC'))
        ]);
        $use = new Stmt\TraitUse([new Name('SomeTrait')]);

        $node = $this->createEnumBuilder('Test')
            ->addStmt($method)
            ->addStmt($enumCase)
            ->addStmts([$const, $use])
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Enum_('Test', [
                'stmts' => [$use, $enumCase, $const, $method]
            ]),
            $node
        );
    }

    public function testDocComment() {
        $docComment = <<<'DOC'
/**
 * Test
 */
DOC;
        $enum = $this->createEnumBuilder('Test')
            ->setDocComment($docComment)
            ->getNode();

        $this->assertEquals(
            new Stmt\Enum_('Test', [], [
                'comments' => [
                    new Comment\Doc($docComment)
                ]
            ]),
            $enum
        );

        $enum = $this->createEnumBuilder('Test')
            ->setDocComment(new Comment\Doc($docComment))
            ->getNode();

        $this->assertEquals(
            new Stmt\Enum_('Test', [], [
                'comments' => [
                    new Comment\Doc($docComment)
                ]
            ]),
            $enum
        );
    }

    public function testAddAttribute() {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new LNumber(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $enum = $this->createEnumBuilder('ATTR_GROUP')
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Stmt\Enum_('ATTR_GROUP', [
                'attrGroups' => [
                    $attributeGroup,
                ]
            ], []),
            $enum
        );
    }

    public function testInvalidStmtError() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unexpected node of type "Stmt_PropertyProperty"');
        $this->createEnumBuilder('Test')
            ->addStmt(new Stmt\PropertyProperty('property'))
        ;
    }

    public function testInvalidDocComment() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Doc comment must be a string or an instance of PhpParser\Comment\Doc');
        $this->createEnumBuilder('Test')
            ->setDocComment(new Comment('Test'));
    }

    public function testEmptyName() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        $this->createEnumBuilder('Test')
            ->implement('');
    }

    public function testInvalidName() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name must be a string or an instance of Node\Name');
        $this->createEnumBuilder('Test')
            ->implement(['Foo']);
    }
}
