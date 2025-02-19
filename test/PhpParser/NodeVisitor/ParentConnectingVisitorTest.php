<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class ParentConnectingVisitorTest extends \PHPUnit\Framework\TestCase {
    public function testConnectsChildNodeToParentNode(): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php class C { public function m() {} }'
        );

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new ParentConnectingVisitor());

        $ast = $traverser->traverse($ast);

        $node = (new NodeFinder())->findFirstInstanceof($ast, ClassMethod::class);

        $this->assertSame('C', $node->getAttribute('parent')->name->toString());
    }

    public function testWeakReferences(): void {
        $ast = (new ParserFactory())->createForNewestSupportedVersion()->parse(
            '<?php class C { public function m() {} }'
        );

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new ParentConnectingVisitor(true));

        $ast = $traverser->traverse($ast);

        $node = (new NodeFinder())->findFirstInstanceof($ast, ClassMethod::class);

        $weakReference = $node->getAttribute('weak_parent');
        $this->assertInstanceOf(\WeakReference::class, $weakReference);
        $this->assertSame('C', $weakReference->get()->name->toString());
    }
}
