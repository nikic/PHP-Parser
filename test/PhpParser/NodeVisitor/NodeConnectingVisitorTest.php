<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class NodeConnectingVisitorTest extends \PHPUnit\Framework\TestCase {
    public function testConnectsNodeToItsParentNodeAndItsSiblingNodes(): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php if (true) {} else {}'
        );

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NodeConnectingVisitor());

        $ast = $traverser->traverse($ast);

        $node = (new NodeFinder())->findFirstInstanceof($ast, Else_::class);

        $this->assertSame(If_::class, get_class($node->getAttribute('parent')));
        $this->assertSame(ConstFetch::class, get_class($node->getAttribute('previous')));

        $node = (new NodeFinder())->findFirstInstanceof($ast, ConstFetch::class);

        $this->assertSame(Else_::class, get_class($node->getAttribute('next')));
    }

    /** @dataProvider provideReferenceModes */
    public function testReconnectsReorderedNodes(bool $weakReferences): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php echo 1; echo 2; echo 3;'
        );
        $traverser = new NodeTraverser(new NodeConnectingVisitor($weakReferences));
        $traverser->traverse($ast);
        $ast = [$ast[2], $ast[1], $ast[0]];
        $traverser->traverse($ast);
        $prefix = $weakReferences ? 'weak_' : '';
        $this->assertNull($ast[0]->getAttribute($prefix . 'previous'));
        $this->assertNull($ast[2]->getAttribute($prefix . 'next'));
        $previous = $ast[1]->getAttribute($prefix . 'previous');
        $next = $ast[1]->getAttribute($prefix . 'next');
        $this->assertSame($ast[0], $weakReferences ? $previous->get() : $previous);
        $this->assertSame($ast[2], $weakReferences ? $next->get() : $next);
    }

    /** @dataProvider provideReferenceModes */
    public function testReconnectsPromotedNode(bool $weakReferences): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php if (true) { echo 1; echo 2; }'
        );
        $traverser = new NodeTraverser(new NodeConnectingVisitor($weakReferences));
        $traverser->traverse($ast);
        $this->assertInstanceOf(If_::class, $ast[0]);
        $node = $ast[0]->stmts[0];
        $attributes = $node->getAttributes();
        $traverser->traverse([$node]);
        $prefix = $weakReferences ? 'weak_' : '';
        $this->assertNull($node->getAttribute($prefix . 'parent'));
        $this->assertNull($node->getAttribute($prefix . 'previous'));
        $this->assertNull($node->getAttribute($prefix . 'next'));
        $this->assertSame($attributes['startLine'], $node->getStartLine());
    }

    /** @dataProvider provideReferenceModes */
    public function testReconnectsAfterRemovingSiblings(bool $weakReferences): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php echo 1; echo 2; echo 3;'
        );
        $traverser = new NodeTraverser(new NodeConnectingVisitor($weakReferences));
        $traverser->traverse($ast);
        $node = $ast[1];
        $node->setAttribute('custom', 'preserved');
        $otherAttribute = $weakReferences ? 'next' : 'weak_next';
        $node->setAttribute($otherAttribute, 'independent-mode');
        $traverser->traverse([$node]);
        $prefix = $weakReferences ? 'weak_' : '';
        $this->assertFalse($node->hasAttribute($prefix . 'previous'));
        $this->assertFalse($node->hasAttribute($prefix . 'next'));
        $this->assertSame('preserved', $node->getAttribute('custom'));
        $this->assertSame('independent-mode', $node->getAttribute($otherAttribute));
    }

    /** @return array<array{bool}> */
    public function provideReferenceModes(): array {
        return [[false], [true]];
    }

    public function testWeakReferences(): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php if (true) {} else {}'
        );

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NodeConnectingVisitor(true));

        $ast = $traverser->traverse($ast);

        $node = (new NodeFinder())->findFirstInstanceof($ast, Else_::class);

        $this->assertInstanceOf(\WeakReference::class, $node->getAttribute('weak_parent'));
        $this->assertSame(If_::class, get_class($node->getAttribute('weak_parent')->get()));
        $this->assertInstanceOf(\WeakReference::class, $node->getAttribute('weak_previous'));
        $this->assertSame(ConstFetch::class, get_class($node->getAttribute('weak_previous')->get()));

        $node = (new NodeFinder())->findFirstInstanceof($ast, ConstFetch::class);

        $this->assertInstanceOf(\WeakReference::class, $node->getAttribute('weak_next'));
        $this->assertSame(Else_::class, get_class($node->getAttribute('weak_next')->get()));
    }
}
