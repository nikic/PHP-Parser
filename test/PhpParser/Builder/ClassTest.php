<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;

class ClassTest extends \PHPUnit\Framework\TestCase {
    protected function createClassBuilder($class) {
        return new Class_($class);
    }

    public function testExtendsImplements(): void {
        $node = $this->createClassBuilder('SomeLogger')
            ->extend('BaseLogger')
            ->implement('Namespaced\Logger', new Name('SomeInterface'))
            ->implement('\Fully\Qualified', 'namespace\NamespaceRelative')
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('SomeLogger', [
                'extends' => new Name('BaseLogger'),
                'implements' => [
                    new Name('Namespaced\Logger'),
                    new Name('SomeInterface'),
                    new Name\FullyQualified('Fully\Qualified'),
                    new Name\Relative('NamespaceRelative'),
                ],
            ]),
            $node
        );
    }

    public function testAbstract(): void {
        $node = $this->createClassBuilder('Test')
            ->makeAbstract()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', [
                'flags' => Modifiers::ABSTRACT
            ]),
            $node
        );
    }

    public function testFinal(): void {
        $node = $this->createClassBuilder('Test')
            ->makeFinal()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', [
                'flags' => Modifiers::FINAL
            ]),
            $node
        );
    }

    public function testReadonly(): void {
        $node = $this->createClassBuilder('Test')
            ->makeReadonly()
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', [
                'flags' => Modifiers::READONLY
            ]),
            $node
        );
    }

    public function testStatementOrder(): void {
        $method = new Stmt\ClassMethod('testMethod');
        $property = new Stmt\Property(
            Modifiers::PUBLIC,
            [new Node\PropertyItem('testProperty')]
        );
        $const = new Stmt\ClassConst([
            new Node\Const_('TEST_CONST', new Node\Scalar\String_('ABC'))
        ]);
        $use = new Stmt\TraitUse([new Name('SomeTrait')]);

        $node = $this->createClassBuilder('Test')
            ->addStmt($method)
            ->addStmt($property)
            ->addStmts([$const, $use])
            ->getNode()
        ;

        $this->assertEquals(
            new Stmt\Class_('Test', [
                'stmts' => [$use, $const, $property, $method]
            ]),
            $node
        );
    }

    public function testDocComment(): void {
        $docComment = <<<'DOC'
/**
 * Test
 */
DOC;
        $class = $this->createClassBuilder('Test')
            ->setDocComment($docComment)
            ->getNode();

        $this->assertEquals(
            new Stmt\Class_('Test', [], [
                'comments' => [
                    new Comment\Doc($docComment)
                ]
            ]),
            $class
        );

        $class = $this->createClassBuilder('Test')
            ->setDocComment(new Comment\Doc($docComment))
            ->getNode();

        $this->assertEquals(
            new Stmt\Class_('Test', [], [
                'comments' => [
                    new Comment\Doc($docComment)
                ]
            ]),
            $class
        );
    }

    public function testAddAttribute(): void {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new Int_(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $class = $this->createClassBuilder('ATTR_GROUP')
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(
            new Stmt\Class_('ATTR_GROUP', [
                'attrGroups' => [
                    $attributeGroup,
                ]
            ], []),
            $class
        );
    }

    public function testInvalidStmtError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unexpected node of type "Stmt_Echo"');
        $this->createClassBuilder('Test')
            ->addStmt(new Stmt\Echo_([]))
        ;
    }

    public function testInvalidDocComment(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Doc comment must be a string or an instance of PhpParser\Comment\Doc');
        $this->createClassBuilder('Test')
            ->setDocComment(new Comment('Test'));
    }

    public function testEmptyName(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name cannot be empty');
        $this->createClassBuilder('Test')
            ->extend('');
    }

    public function testInvalidName(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Name must be a string or an instance of Node\Name');
        $this->createClassBuilder('Test')
            ->extend(['Foo']);
    }
}
