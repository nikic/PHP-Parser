<?php
namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use PhpParser\Node\Expr;

/**
 * Class CallableVisitorTest
 * @package PhpParser\NodeVisitor
 */
class CallableVisitorTest extends TestCase
{
    /**
     * @var int
     */
    private $enterNode = 0;

    /**
     * @var int
     */
    private $leaveNode = 0;

    /**
     * @var int
     */
    private $beforeTraverse = 0;

    /**
     * @var int
     */
    private $afterTraverse = 0;

    /**
     * @var array
     */
    private $order = [];

    /**
     * Reset all values.
     */
    protected function setUp()
    {
        $this->enterNode = 0;
        $this->leaveNode = 0;
        $this->beforeTraverse = 0;
        $this->afterTraverse = 0;

        $this->order = [];
    }

    /**
     * Test if the CallableVisitor works as expected with all callables set.
     */
    public function testWithClosures()
    {
        $this->callableVisitorTest(function (Node $node) {
            $this->order[] = 'enterNode';
            $this->enterNode++;
        }, function (Node $node) {
            $this->order[] = 'leaveNode';
            $this->leaveNode++;
        }, function ($array) {
            $this->order[] = 'beforeTraverse';
            $this->beforeTraverse++;
        }, function ($array) {
            $this->order[] = 'afterTraverse';
            $this->afterTraverse++;
        });

        $this->assertSame([
            $this->enterNode,
            $this->leaveNode,
            $this->beforeTraverse,
            $this->afterTraverse
        ], [6, 6, 1, 1]);

        $this->assertSame([
            'beforeTraverse',
            'enterNode',
            'enterNode',
            'enterNode',
            'leaveNode',
            'enterNode',
            'enterNode',
            'leaveNode',
            'enterNode',
            'leaveNode',
            'leaveNode',
            'leaveNode',
            'leaveNode',
            'afterTraverse',
        ], $this->order);
    }

    /**
     * Test if the CallableVisitor works as expected with all enter node/traverse callables set.
     */
    public function testPartiallyFilled()
    {
        $this->callableVisitorTest(function (Node $node) {
            $this->order[] = 'enterNode';
            $this->enterNode++;
        }, null, function ($array) {
            $this->order[] = 'beforeTraverse';
            $this->beforeTraverse++;
        }, null);

        $this->assertSame([
            $this->enterNode,
            $this->leaveNode,
            $this->beforeTraverse,
            $this->afterTraverse
        ], [6, 0, 1, 0]);

        $this->assertSame([
            'beforeTraverse',
            'enterNode',
            'enterNode',
            'enterNode',
            'enterNode',
            'enterNode',
            'enterNode',
        ], $this->order);
    }

    /**
     * @param $enterNode
     * @param $leaveNode
     * @param $beforeTravers
     * @param $afterTravers
     */
    private function callableVisitorTest($enterNode, $leaveNode, $beforeTravers, $afterTravers)
    {
        $traverser = new NodeTraverser();
        $visitor = new CallableVisitor();

        $visitor->setEnterNode($enterNode);
        if (is_callable($enterNode)) {
            $visitor->setLeaveNode($leaveNode);
        }

        $visitor->setBeforeTraverse($beforeTravers);
        if (is_callable($afterTravers)) {
            $visitor->setAfterTraverse($afterTravers);
        }

        $traverser->addVisitor($visitor);

        $assign = new Expr\Assign(new Expr\Variable('a'), new Expr\BinaryOp\Concat(
            new Expr\Variable('b'), new Expr\Variable('c')
        ));
        $stmts = [new Node\Stmt\Expression($assign)];

        $traverser->traverse($stmts);
    }
}