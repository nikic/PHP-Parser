<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;

class NodeTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testNonModifying() {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');
        $echoNode = new Node\Stmt\Echo_(array($str1Node, $str2Node));
        $stmts    = array($echoNode);

        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        $visitor->expects($this->at(0))->method('beforeTraverse')->with($stmts);
        $visitor->expects($this->at(1))->method('enterNode')->with($echoNode);
        $visitor->expects($this->at(2))->method('enterNode')->with($str1Node);
        $visitor->expects($this->at(3))->method('leaveNode')->with($str1Node);
        $visitor->expects($this->at(4))->method('enterNode')->with($str2Node);
        $visitor->expects($this->at(5))->method('leaveNode')->with($str2Node);
        $visitor->expects($this->at(6))->method('leaveNode')->with($echoNode);
        $visitor->expects($this->at(7))->method('afterTraverse')->with($stmts);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testModifying() {
        $str1Node  = new String_('Foo');
        $str2Node  = new String_('Bar');
        $printNode = new Expr\Print_($str1Node);

        // first visitor changes the node, second verifies the change
        $visitor1 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor2 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        // replace empty statements with string1 node
        $visitor1->expects($this->at(0))->method('beforeTraverse')->with(array())
                 ->will($this->returnValue(array($str1Node)));
        $visitor2->expects($this->at(0))->method('beforeTraverse')->with(array($str1Node));

        // replace string1 node with print node
        $visitor1->expects($this->at(1))->method('enterNode')->with($str1Node)
                 ->will($this->returnValue($printNode));
        $visitor2->expects($this->at(1))->method('enterNode')->with($printNode);

        // replace string1 node with string2 node
        $visitor1->expects($this->at(2))->method('enterNode')->with($str1Node)
                 ->will($this->returnValue($str2Node));
        $visitor2->expects($this->at(2))->method('enterNode')->with($str2Node);

        // replace string2 node with string1 node again
        $visitor1->expects($this->at(3))->method('leaveNode')->with($str2Node)
                 ->will($this->returnValue($str1Node));
        $visitor2->expects($this->at(3))->method('leaveNode')->with($str1Node);

        // replace print node with string1 node again
        $visitor1->expects($this->at(4))->method('leaveNode')->with($printNode)
                 ->will($this->returnValue($str1Node));
        $visitor2->expects($this->at(4))->method('leaveNode')->with($str1Node);

        // replace string1 node with empty statements again
        $visitor1->expects($this->at(5))->method('afterTraverse')->with(array($str1Node))
                 ->will($this->returnValue(array()));
        $visitor2->expects($this->at(5))->method('afterTraverse')->with(array());

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        // as all operations are reversed we end where we start
        $this->assertEquals(array(), $traverser->traverse(array()));
    }

    public function testRemove() {
        $str1Node = new String_('Foo');
        $str2Node = new String_('Bar');

        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        // remove the string1 node, leave the string2 node
        $visitor->expects($this->at(2))->method('leaveNode')->with($str1Node)
                ->will($this->returnValue(false));

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals(array($str2Node), $traverser->traverse(array($str1Node, $str2Node)));
    }

    public function testMerge() {
        $strStart  = new String_('Start');
        $strMiddle = new String_('End');
        $strEnd    = new String_('Middle');
        $strR1     = new String_('Replacement 1');
        $strR2     = new String_('Replacement 2');

        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        // replace strMiddle with strR1 and strR2 by merge
        $visitor->expects($this->at(4))->method('leaveNode')->with($strMiddle)
                ->will($this->returnValue(array($strR1, $strR2)));

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals(
            array($strStart, $strR1, $strR2, $strEnd),
            $traverser->traverse(array($strStart, $strMiddle, $strEnd))
        );
    }

    public function testDeepArray() {
        $strNode = new String_('Foo');
        $stmts = array(array(array($strNode)));

        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(1))->method('enterNode')->with($strNode);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testDontTraverseChildren() {
        $strNode = new String_('str');
        $printNode = new Expr\Print_($strNode);
        $varNode = new Expr\Variable('foo');
        $mulNode = new Expr\BinaryOp\Mul($varNode, $varNode);
        $negNode = new Expr\UnaryMinus($mulNode);
        $stmts = array($printNode, $negNode);

        $visitor1 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor2 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        $visitor1->expects($this->at(1))->method('enterNode')->with($printNode)
            ->will($this->returnValue(NodeTraverser::DONT_TRAVERSE_CHILDREN));
        $visitor2->expects($this->at(1))->method('enterNode')->with($printNode);

        $visitor1->expects($this->at(2))->method('leaveNode')->with($printNode);
        $visitor2->expects($this->at(2))->method('leaveNode')->with($printNode);

        $visitor1->expects($this->at(3))->method('enterNode')->with($negNode);
        $visitor2->expects($this->at(3))->method('enterNode')->with($negNode);

        $visitor1->expects($this->at(4))->method('enterNode')->with($mulNode);
        $visitor2->expects($this->at(4))->method('enterNode')->with($mulNode)
            ->will($this->returnValue(NodeTraverser::DONT_TRAVERSE_CHILDREN));

        $visitor1->expects($this->at(5))->method('leaveNode')->with($mulNode);
        $visitor2->expects($this->at(5))->method('leaveNode')->with($mulNode);

        $visitor1->expects($this->at(6))->method('leaveNode')->with($negNode);
        $visitor2->expects($this->at(6))->method('leaveNode')->with($negNode);

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);

        $this->assertEquals($stmts, $traverser->traverse($stmts));
    }

    public function testStopTraversal() {
        $varNode1 = new Expr\Variable('a');
        $varNode2 = new Expr\Variable('b');
        $varNode3 = new Expr\Variable('c');
        $mulNode = new Expr\BinaryOp\Mul($varNode1, $varNode2);
        $printNode = new Expr\Print_($varNode3);
        $stmts = [$mulNode, $printNode];

        // From enterNode() with array parent
        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(1))->method('enterNode')->with($mulNode)
            ->will($this->returnValue(NodeTraverser::STOP_TRAVERSAL));
        $visitor->expects($this->at(2))->method('afterTraversal');
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));

        // From enterNode with Node parent
        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(2))->method('enterNode')->with($varNode1)
            ->will($this->returnValue(NodeTraverser::STOP_TRAVERSAL));
        $visitor->expects($this->at(3))->method('afterTraversal');
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));

        // From leaveNode with Node parent
        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(3))->method('leaveNode')->with($varNode1)
            ->will($this->returnValue(NodeTraverser::STOP_TRAVERSAL));
        $visitor->expects($this->at(4))->method('afterTraversal');
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));

        // From leaveNode with array parent
        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(6))->method('leaveNode')->with($mulNode)
            ->will($this->returnValue(NodeTraverser::STOP_TRAVERSAL));
        $visitor->expects($this->at(7))->method('afterTraversal');
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals($stmts, $traverser->traverse($stmts));

        // Check that pending array modifications are still carried out
        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->expects($this->at(6))->method('leaveNode')->with($mulNode)
            ->will($this->returnValue(NodeTraverser::REMOVE_NODE));
        $visitor->expects($this->at(7))->method('enterNode')->with($printNode)
            ->will($this->returnValue(NodeTraverser::STOP_TRAVERSAL));
        $visitor->expects($this->at(8))->method('afterTraversal');
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $this->assertEquals([$printNode], $traverser->traverse($stmts));

    }

    public function testRemovingVisitor() {
        $visitor1 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor2 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor3 = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor1);
        $traverser->addVisitor($visitor2);
        $traverser->addVisitor($visitor3);

        $preExpected = array($visitor1, $visitor2, $visitor3);
        $this->assertAttributeSame($preExpected, 'visitors', $traverser, 'The appropriate visitors have not been added');

        $traverser->removeVisitor($visitor2);

        $postExpected = array(0 => $visitor1, 2 => $visitor3);
        $this->assertAttributeSame($postExpected, 'visitors', $traverser, 'The appropriate visitors are not present after removal');
    }

    public function testNoCloneNodes() {
        $stmts = array(new Node\Stmt\Echo_(array(new String_('Foo'), new String_('Bar'))));

        $traverser = new NodeTraverser;

        $this->assertSame($stmts, $traverser->traverse($stmts));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage leaveNode() may only return an array if the parent structure is an array
     */
    public function testReplaceByArrayOnlyAllowedIfParentIsArray() {
        $stmts = array(new Node\Expr\UnaryMinus(new Node\Scalar\LNumber(42)));

        $visitor = $this->getMockBuilder('PhpParser\NodeVisitor')->getMock();
        $visitor->method('leaveNode')->willReturn(array(new Node\Scalar\DNumber(42.0)));

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($stmts);
    }
}
