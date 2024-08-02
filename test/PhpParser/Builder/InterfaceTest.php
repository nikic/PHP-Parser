<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;

class InterfaceTest extends \PHPUnit\Framework\TestCase {
    protected function createInterfaceBuilder() {
        return new Interface_('Contract');
    }

    private function dump($node) {
        $pp = new \PhpParser\PrettyPrinter\Standard();
        return $pp->prettyPrint([$node]);
    }

    public function testEmpty(): void {
        $contract = $this->createInterfaceBuilder()->getNode();
        $this->assertInstanceOf(Stmt\Interface_::class, $contract);
        $this->assertEquals(new Node\Identifier('Contract'), $contract->name);
    }

    public function testExtending(): void {
        $contract = $this->createInterfaceBuilder()
            ->extend('Space\Root1', 'Root2')->getNode();
        $this->assertEquals(
            new Stmt\Interface_('Contract', [
                'extends' => [
                    new Node\Name('Space\Root1'),
                    new Node\Name('Root2')
                ],
            ]), $contract
        );
    }

    public function testAddMethod(): void {
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->createInterfaceBuilder()->addStmt($method)->getNode();
        $this->assertSame([$method], $contract->stmts);
    }

    public function testAddConst(): void {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new Float_(299792458.0))
        ]);
        $contract = $this->createInterfaceBuilder()->addStmt($const)->getNode();
        $this->assertSame(299792458.0, $contract->stmts[0]->consts[0]->value->value);
    }

    public function testOrder(): void {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new Float_(299792458))
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->createInterfaceBuilder()
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        $this->assertInstanceOf(Stmt\ClassConst::class, $contract->stmts[0]);
        $this->assertInstanceOf(Stmt\ClassMethod::class, $contract->stmts[1]);
    }

    public function testDocComment(): void {
        $node = $this->createInterfaceBuilder()
            ->setDocComment('/** Test */')
            ->getNode();

        $this->assertEquals(new Stmt\Interface_('Contract', [], [
            'comments' => [new Comment\Doc('/** Test */')]
        ]), $node);
    }

    public function testAddAttribute(): void {
        $attribute = new Attribute(
            new Name('Attr'),
            [new Arg(new Int_(1), false, false, [], new Identifier('name'))]
        );
        $attributeGroup = new AttributeGroup([$attribute]);

        $node = $this->createInterfaceBuilder()
            ->addAttribute($attributeGroup)
            ->getNode();

        $this->assertEquals(new Stmt\Interface_('Contract', [
            'attrGroups' => [$attributeGroup],
        ], []), $node);
    }

    public function testInvalidStmtError(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unexpected node of type "PropertyItem"');
        $this->createInterfaceBuilder()->addStmt(new Node\PropertyItem('invalid'));
    }

    public function testFullFunctional(): void {
        $const = new Stmt\ClassConst([
            new Node\Const_('SPEED_OF_LIGHT', new Float_(299792458))
        ]);
        $method = new Stmt\ClassMethod('doSomething');
        $contract = $this->createInterfaceBuilder()
            ->addStmt($method)
            ->addStmt($const)
            ->getNode()
        ;

        eval($this->dump($contract));

        $this->assertTrue(interface_exists('Contract', false));
    }
}
