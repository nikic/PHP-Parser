<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;

class NodeTraverserTest extends \PHPUnit\Framework\TestCase
{
    public function testNonModifying() {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');
        $echoNode = new Node\Stmt\Echo_([$str1Node, $str2Node]);
        $stmts    = [$echoNode];

        $visitor = new NodeVisitorForTesting();
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $echoNode],
            ['enterNode', $str1Node],
            ['leaveNode', $str1Node],
            ['enterNode', $str2Node],
            ['leaveNode', $str2Node],
            ['leaveNode', $echoNode],
            ['afterTraverse', $stmts],
        ], $visitor->trace);
    }

    public function testModifying() {
        $str1Node  = new String_('Foo');
        $str2Node  = new String_('Bar');
        $printNode = new Expr\Print_($str1Node);

        // first visitor changes the node, second verifies the change
        $visitor1 = new NodeVisitorForTesting([
            ['beforeTraverse', [], [$str1Node]],
            ['enterNode', $str1Node, $printNode],
            ['enterNode', $str1Node, $str2Node],
            ['leaveNode', $str2Node, $str1Node],
            ['leaveNode', $printNode, $str1Node],
            ['afterTraverse', [$str1Node], []],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        // as all operations are reversed we end where we start
        $this->assertEquals([], $traverser->traverse([]));
        $this->assertEquals([
            ['beforeTraverse', [$str1Node]],
            ['enterNode', $printNode],
            ['enterNode', $str2Node],
            ['leaveNode', $str1Node],
            ['leaveNode', $str1Node],
            ['afterTraverse', []],
        ], $visitor2->trace);
    }

    public function testRemove() {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');

        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $str1Node, NodeTraverser::REMOVE_NODE],
        ]);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals([$str2Node], $traverser->traverse([$str1Node, $str2Node]));
    }

    public function testMerge() {
        $strStart  = new String_('Start');
        $strMiddle = new String_('End');
        $strEnd    = new String_('Middle');
        $strR1     = new String_('Replacement 1');
        $strR2     = new String_('Replacement 2');

        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $strMiddle, [$strR1, $strR2]],
        ]);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals(
            [$strStart, $strR1, $strR2, $strEnd],
            $traverser->traverse([$strStart, $strMiddle, $strEnd])
        );
    }

    public function testInvalidDeepArray() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid node structure: Contains nested arrays');
        $strNode = new String_('Foo');
        $stmts = [[[$strNode]]];

        $traverser = new NodeTraverser;
        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testDontTraverseChildren() {
        $strNode = new String_('str');
        $printNode = new Expr\Print_($strNode);
        $varNode = new Expr\Variable('foo');
        $mulNode = new Expr\BinaryOp\Mul($varNode, $varNode);
        $negNode = new Expr\UnaryMinus($mulNode);
        $stmts = [$printNode, $negNode];

        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $printNode, NodeTraverser::DONT_TRAVERSE_CHILDREN],
        ]);
        $visitor2 = new NodeVisitorForTesting([
            ['enterNode', $mulNode, NodeTraverser::DONT_TRAVERSE_CHILDREN],
        ]);

        $expectedTrace = [
            ['beforeTraverse', $stmts],
            ['enterNode', $printNode],
            ['leaveNode', $printNode],
            ['enterNode', $negNode],
            ['enterNode', $mulNode],
            ['leaveNode', $mulNode],
            ['leaveNode', $negNode],
            ['afterTraverse', $stmts],
        ];

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals($expectedTrace, $visitor1->trace);
        $this->assertEquals($expectedTrace, $visitor2->trace);
    }

    public function testDontTraverseCurrentAndChildren() {
        // print 'str'; -($foo * $foo);
        $strNode = new String_('str');
        $printNode = new Expr\Print_($strNode);
        $varNode = new Expr\Variable('foo');
        $mulNode = new Expr\BinaryOp\Mul($varNode, $varNode);
        $divNode = new Expr\BinaryOp\Div($varNode, $varNode);
        $negNode = new Expr\UnaryMinus($mulNode);
        $stmts = [$printNode, $negNode];

        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $printNode, NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN],
            ['enterNode', $mulNode, NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN],
            ['leaveNode', $mulNode, $divNode],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        $resultStmts = $traverser->traverse($stmts);
        $this->assertInstanceOf(Expr\BinaryOp\Div::class, $resultStmts[1]->expr);

        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $printNode],
            ['leaveNode', $printNode],
            ['enterNode', $negNode],
            ['enterNode', $mulNode],
            ['leaveNode', $mulNode],
            ['leaveNode', $negNode],
            ['afterTraverse', $resultStmts],
        ], $visitor1->trace);
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $negNode],
            ['leaveNode', $negNode],
            ['afterTraverse', $resultStmts],
        ], $visitor2->trace);
    }

    public function testStopTraversal() {
        $varNode1 = new Expr\Variable('a');
        $varNode2 = new Expr\Variable('b');
        $varNode3 = new Expr\Variable('c');
        $mulNode = new Expr\BinaryOp\Mul($varNode1, $varNode2);
        $printNode = new Expr\Print_($varNode3);
        $stmts = [$mulNode, $printNode];

        // From enterNode() with array parent
        $visitor = new NodeVisitorForTesting([
            ['enterNode', $mulNode, NodeTraverser::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['afterTraverse', $stmts],
        ], $visitor->trace);

        // From enterNode with Node parent
        $visitor = new NodeVisitorForTesting([
            ['enterNode', $varNode1, NodeTraverser::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['enterNode', $varNode1],
            ['afterTraverse', $stmts],
        ], $visitor->trace);

        // From leaveNode with Node parent
        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $varNode1, NodeTraverser::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['enterNode', $varNode1],
            ['leaveNode', $varNode1],
            ['afterTraverse', $stmts],
        ], $visitor->trace);

        // From leaveNode with array parent
        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $mulNode, NodeTraverser::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['enterNode', $varNode1],
            ['leaveNode', $varNode1],
            ['enterNode', $varNode2],
            ['leaveNode', $varNode2],
            ['leaveNode', $mulNode],
            ['afterTraverse', $stmts],
        ], $visitor->trace);

        // Check that pending array modifications are still carried out
        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $mulNode, NodeTraverser::REMOVE_NODE],
            ['enterNode', $printNode, NodeTraverser::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals([$printNode], $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['enterNode', $varNode1],
            ['leaveNode', $varNode1],
            ['enterNode', $varNode2],
            ['leaveNode', $varNode2],
            ['leaveNode', $mulNode],
            ['enterNode', $printNode],
            ['afterTraverse', [$printNode]],
        ], $visitor->trace);
    }

    public function testRemovingVisitor() {
        $visitor1 = new class extends NodeVisitorAbstract {};
        $visitor2 = new class extends NodeVisitorAbstract {};
        $visitor3 = new class extends NodeVisitorAbstract {};

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);
        $traverser->addVisitor($visitor3);

        $getVisitors = (function () {
            return $this->visitors;
        })->bindTo($traverser, NodeTraverser::class);

        $preExpected = [$visitor1, $visitor2, $visitor3];
        $this->assertSame($preExpected, $getVisitors());

        $traverser->removeVisitor($visitor2);

        $postExpected = [0 => $visitor1, 2 => $visitor3];
        $this->assertSame($postExpected, $getVisitors());
    }

    public function testNoCloneNodes() {
        $stmts = [new Node\Stmt\Echo_([new String_('Foo'), new String_('Bar')])];

        $traverser = new NodeTraverser;

        $this->assertSame($stmts, $traverser->traverse($stmts));
    }

    /**
     * @dataProvider provideTestInvalidReturn
     */
    public function testInvalidReturn($stmts, $visitor, $message) {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage($message);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($stmts);
    }

    public function provideTestInvalidReturn() {
        $num = new Node\Scalar\LNumber(42);
        $expr = new Node\Stmt\Expression($num);
        $stmts = [$expr];

        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $expr, 'foobar'],
        ]);
        $visitor2 = new NodeVisitorForTesting([
            ['enterNode', $num, 'foobar'],
        ]);
        $visitor3 = new NodeVisitorForTesting([
            ['leaveNode', $num, 'foobar'],
        ]);
        $visitor4 = new NodeVisitorForTesting([
            ['leaveNode', $expr, 'foobar'],
        ]);
        $visitor5 = new NodeVisitorForTesting([
            ['leaveNode', $num, [new Node\Scalar\DNumber(42.0)]],
        ]);
        $visitor6 = new NodeVisitorForTesting([
            ['leaveNode', $expr, false],
        ]);
        $visitor7 = new NodeVisitorForTesting([
            ['enterNode', $expr, new Node\Scalar\LNumber(42)],
        ]);
        $visitor8 = new NodeVisitorForTesting([
            ['enterNode', $num, new Node\Stmt\Return_()],
        ]);

        return [
            [$stmts, $visitor1, 'enterNode() returned invalid value of type string'],
            [$stmts, $visitor2, 'enterNode() returned invalid value of type string'],
            [$stmts, $visitor3, 'leaveNode() returned invalid value of type string'],
            [$stmts, $visitor4, 'leaveNode() returned invalid value of type string'],
            [$stmts, $visitor5, 'leaveNode() may only return an array if the parent structure is an array'],
            [$stmts, $visitor6, 'bool(false) return from leaveNode() no longer supported. Return NodeTraverser::REMOVE_NODE instead'],
            [$stmts, $visitor7, 'Trying to replace statement (Stmt_Expression) with expression (Scalar_LNumber). Are you missing a Stmt_Expression wrapper?'],
            [$stmts, $visitor8, 'Trying to replace expression (Scalar_LNumber) with statement (Stmt_Return)'],
        ];
    }
}
