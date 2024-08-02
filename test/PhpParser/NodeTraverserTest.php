<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;

class NodeTraverserTest extends \PHPUnit\Framework\TestCase {
    public function testNonModifying(): void {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');
        $echoNode = new Node\Stmt\Echo_([$str1Node, $str2Node]);
        $stmts    = [$echoNode];

        $visitor = new NodeVisitorForTesting();
        $traverser = new NodeTraverser();
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

    public function testModifying(): void {
        $str1Node  = new String_('Foo');
        $str2Node  = new String_('Bar');
        $printNode = new Expr\Print_($str1Node);

        // Visitor 2 performs changes, visitors 1 and 3 observe the changes.
        $visitor1 = new NodeVisitorForTesting();
        $visitor2 = new NodeVisitorForTesting([
            ['beforeTraverse', [], [$str1Node]],
            ['enterNode', $str1Node, $printNode],
            ['enterNode', $str1Node, $str2Node],
            ['leaveNode', $str2Node, $str1Node],
            ['leaveNode', $printNode, $str1Node],
            ['afterTraverse', [$str1Node], []],
        ]);
        $visitor3 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser($visitor1, $visitor2, $visitor3);

        // as all operations are reversed we end where we start
        $this->assertEquals([], $traverser->traverse([]));
        $this->assertEquals([
            // Sees nodes before changes on entry.
            ['beforeTraverse', []],
            ['enterNode', $str1Node],
            ['enterNode', $str1Node],
            // Sees nodes after changes on leave.
            ['leaveNode', $str1Node],
            ['leaveNode', $str1Node],
            ['afterTraverse', []],
        ], $visitor1->trace);
        $this->assertEquals([
            // Sees nodes after changes on entry.
            ['beforeTraverse', [$str1Node]],
            ['enterNode', $printNode],
            ['enterNode', $str2Node],
            // Sees nodes before changes on leave.
            ['leaveNode', $str2Node],
            ['leaveNode', $printNode],
            ['afterTraverse', [$str1Node]],
        ], $visitor3->trace);
    }

    public function testRemoveFromLeave(): void {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');

        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $str1Node, NodeVisitor::REMOVE_NODE],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor2);
        $traverser->addVisitor($visitor);

        $stmts = [$str1Node, $str2Node];
        $this->assertEquals([$str2Node], $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $str1Node],
            ['enterNode', $str2Node],
            ['leaveNode', $str2Node],
            ['afterTraverse', [$str2Node]],
        ], $visitor2->trace);
    }

    public function testRemoveFromEnter(): void {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');

        $visitor = new NodeVisitorForTesting([
            ['enterNode', $str1Node, NodeVisitor::REMOVE_NODE],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->addVisitor($visitor2);

        $stmts = [$str1Node, $str2Node];
        $this->assertEquals([$str2Node], $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $str2Node],
            ['leaveNode', $str2Node],
            ['afterTraverse', [$str2Node]],
        ], $visitor2->trace);
    }

    public function testReturnArrayFromEnter(): void {
        $str1Node = new String_('Str1');
        $str2Node = new String_('Str2');
        $str3Node = new String_('Str3');
        $str4Node = new String_('Str4');

        $visitor = new NodeVisitorForTesting([
            ['enterNode', $str1Node, [$str3Node, $str4Node]],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->addVisitor($visitor2);

        $stmts = [$str1Node, $str2Node];
        $this->assertEquals([$str3Node, $str4Node, $str2Node], $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $str2Node],
            ['leaveNode', $str2Node],
            ['afterTraverse', [$str3Node, $str4Node, $str2Node]],
        ], $visitor2->trace);
    }

    public function testMerge(): void {
        $strStart  = new String_('Start');
        $strMiddle = new String_('End');
        $strEnd    = new String_('Middle');
        $strR1     = new String_('Replacement 1');
        $strR2     = new String_('Replacement 2');

        $visitor = new NodeVisitorForTesting([
            ['leaveNode', $strMiddle, [$strR1, $strR2]],
        ]);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);

        $this->assertEquals(
            [$strStart, $strR1, $strR2, $strEnd],
            $traverser->traverse([$strStart, $strMiddle, $strEnd])
        );
    }

    public function testInvalidDeepArray(): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid node structure: Contains nested arrays');
        $strNode = new String_('Foo');
        $stmts = [[[$strNode]]];

        $traverser = new NodeTraverser();
        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testDontTraverseChildren(): void {
        $strNode = new String_('str');
        $printNode = new Expr\Print_($strNode);
        $varNode = new Expr\Variable('foo');
        $mulNode = new Expr\BinaryOp\Mul($varNode, $varNode);
        $negNode = new Expr\UnaryMinus($mulNode);
        $stmts = [$printNode, $negNode];

        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $printNode, NodeVisitor::DONT_TRAVERSE_CHILDREN],
        ]);
        $visitor2 = new NodeVisitorForTesting([
            ['enterNode', $mulNode, NodeVisitor::DONT_TRAVERSE_CHILDREN],
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

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals($expectedTrace, $visitor1->trace);
        $this->assertEquals($expectedTrace, $visitor2->trace);
    }

    public function testDontTraverseCurrentAndChildren(): void {
        // print 'str'; -($foo * $foo);
        $strNode = new String_('str');
        $printNode = new Expr\Print_($strNode);
        $varNode = new Expr\Variable('foo');
        $mulNode = new Expr\BinaryOp\Mul($varNode, $varNode);
        $divNode = new Expr\BinaryOp\Div($varNode, $varNode);
        $negNode = new Expr\UnaryMinus($mulNode);
        $stmts = [$printNode, $negNode];

        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $printNode, NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN],
            ['enterNode', $mulNode, NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN],
            ['leaveNode', $mulNode, $divNode],
        ]);
        $visitor2 = new NodeVisitorForTesting();

        $traverser = new NodeTraverser();
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

    public function testStopTraversal(): void {
        $varNode1 = new Expr\Variable('a');
        $varNode2 = new Expr\Variable('b');
        $varNode3 = new Expr\Variable('c');
        $mulNode = new Expr\BinaryOp\Mul($varNode1, $varNode2);
        $printNode = new Expr\Print_($varNode3);
        $stmts = [$mulNode, $printNode];

        // From enterNode() with array parent
        $visitor = new NodeVisitorForTesting([
            ['enterNode', $mulNode, NodeVisitor::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $mulNode],
            ['afterTraverse', $stmts],
        ], $visitor->trace);

        // From enterNode with Node parent
        $visitor = new NodeVisitorForTesting([
            ['enterNode', $varNode1, NodeVisitor::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser();
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
            ['leaveNode', $varNode1, NodeVisitor::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser();
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
            ['leaveNode', $mulNode, NodeVisitor::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser();
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
            ['leaveNode', $mulNode, NodeVisitor::REMOVE_NODE],
            ['enterNode', $printNode, NodeVisitor::STOP_TRAVERSAL],
        ]);
        $traverser = new NodeTraverser();
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

    public function testReplaceWithNull(): void {
        $one = new Int_(1);
        $else1 = new Else_();
        $else2 = new Else_();
        $if1 = new If_($one, ['else' => $else1]);
        $if2 = new If_($one, ['else' => $else2]);
        $stmts = [$if1, $if2];
        $visitor1 = new NodeVisitorForTesting([
            ['enterNode', $else1, NodeVisitor::REPLACE_WITH_NULL],
            ['leaveNode', $else2, NodeVisitor::REPLACE_WITH_NULL],
        ]);
        $visitor2 = new NodeVisitorForTesting();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);
        $newStmts = $traverser->traverse($stmts);
        $this->assertEquals([
            new If_($one),
            new If_($one),
        ], $newStmts);
        $this->assertEquals([
            ['beforeTraverse', $stmts],
            ['enterNode', $if1],
            ['enterNode', $one],
            // We never see the if1 Else node.
            ['leaveNode', $one],
            ['leaveNode', $if1],
            ['enterNode', $if2],
            ['enterNode', $one],
            ['leaveNode', $one],
            // We do see the if2 Else node, as it will only be replaced afterwards.
            ['enterNode', $else2],
            ['leaveNode', $else2],
            ['leaveNode', $if2],
            ['afterTraverse', $stmts],
        ], $visitor2->trace);
    }

    public function testRemovingVisitor(): void {
        $visitor1 = new class () extends NodeVisitorAbstract {};
        $visitor2 = new class () extends NodeVisitorAbstract {};
        $visitor3 = new class () extends NodeVisitorAbstract {};

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);
        $traverser->addVisitor($visitor3);

        $getVisitors = (function () {
            return $this->visitors;
        })->bindTo($traverser, NodeTraverser::class);

        $preExpected = [$visitor1, $visitor2, $visitor3];
        $this->assertSame($preExpected, $getVisitors());

        $traverser->removeVisitor($visitor2);

        $postExpected = [$visitor1, $visitor3];
        $this->assertSame($postExpected, $getVisitors());
    }

    public function testNoCloneNodes(): void {
        $stmts = [new Node\Stmt\Echo_([new String_('Foo'), new String_('Bar')])];

        $traverser = new NodeTraverser();

        $this->assertSame($stmts, $traverser->traverse($stmts));
    }

    /**
     * @dataProvider provideTestInvalidReturn
     */
    public function testInvalidReturn($stmts, $visitor, $message): void {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage($message);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($stmts);
    }

    public static function provideTestInvalidReturn() {
        $num = new Node\Scalar\Int_(42);
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
            ['leaveNode', $num, [new Node\Scalar\Float_(42.0)]],
        ]);
        $visitor6 = new NodeVisitorForTesting([
            ['leaveNode', $expr, false],
        ]);
        $visitor7 = new NodeVisitorForTesting([
            ['enterNode', $expr, new Node\Scalar\Int_(42)],
        ]);
        $visitor8 = new NodeVisitorForTesting([
            ['enterNode', $num, new Node\Stmt\Return_()],
        ]);
        $visitor9 = new NodeVisitorForTesting([
            ['enterNode', $expr, NodeVisitor::REPLACE_WITH_NULL],
        ]);
        $visitor10 = new NodeVisitorForTesting([
            ['leaveNode', $expr, NodeVisitor::REPLACE_WITH_NULL],
        ]);

        return [
            [$stmts, $visitor1, 'enterNode() returned invalid value of type string'],
            [$stmts, $visitor2, 'enterNode() returned invalid value of type string'],
            [$stmts, $visitor3, 'leaveNode() returned invalid value of type string'],
            [$stmts, $visitor4, 'leaveNode() returned invalid value of type string'],
            [$stmts, $visitor5, 'leaveNode() may only return an array if the parent structure is an array'],
            [$stmts, $visitor6, 'leaveNode() returned invalid value of type bool'],
            [$stmts, $visitor7, 'Trying to replace statement (Stmt_Expression) with expression (Scalar_Int). Are you missing a Stmt_Expression wrapper?'],
            [$stmts, $visitor8, 'Trying to replace expression (Scalar_Int) with statement (Stmt_Return)'],
            [$stmts, $visitor9, 'REPLACE_WITH_NULL can not be used if the parent structure is an array'],
            [$stmts, $visitor10, 'REPLACE_WITH_NULL can not be used if the parent structure is an array'],
        ];
    }
}
