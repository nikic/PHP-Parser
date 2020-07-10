<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;
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

        $traverser = new NodeTraverser;

        $visitor = new FindingVisitor(
            static function(Node $node) {
                return $node instanceof Else_;
            }
        );

        $traverser->addVisitor($visitor);

        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($ast);

        $this->assertSame(If_::class, get_class($visitor->getFoundNodes()[0]->getAttribute('parent')));
        $this->assertSame(ConstFetch::class, get_class($visitor->getFoundNodes()[0]->getAttribute('previous')));

        $traverser = new NodeTraverser;

        $visitor = new FindingVisitor(
            static function(Node $node) {
                return $node instanceof ConstFetch;
            }
        );

        $traverser->addVisitor($visitor);

        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($ast);

        $this->assertSame(Else_::class, get_class($visitor->getFoundNodes()[0]->getAttribute('next')));
    }
}
