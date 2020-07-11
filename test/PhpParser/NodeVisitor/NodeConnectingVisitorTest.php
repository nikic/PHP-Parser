<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class NodeConnectingVisitorTest extends \PHPUnit\Framework\TestCase
{
    public function testConnectsNodeToItsParentNodeAndItsSiblingNodes()
    {
        $ast = (new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse(
            '<?php if (true) {} else {}'
        );

        $traverser = new NodeTraverser;

        $traverser->addVisitor(new NodeConnectingVisitor);

        $ast = $traverser->traverse($ast);

        $node = (new NodeFinder)->findFirstInstanceof($ast, Else_::class);

        $this->assertSame(If_::class, get_class($node->getAttribute('parent')));
        $this->assertSame(ConstFetch::class, get_class($node->getAttribute('previous')));

        $node = (new NodeFinder)->findFirstInstanceof($ast, ConstFetch::class);

        $this->assertSame(Else_::class, get_class($node->getAttribute('next')));
    }
}
