Frequently Asked Questions
==========================

 * [How can the parent of a node be obtained?](#how-can-the-parent-of-a-node-be-obtained)
 * [How can the next/previous sibling of a node be obtained?](#how-can-the-nextprevious-sibling-of-a-node-be-obtained)

How can the parent of a node be obtained?
-----

The AST does not store parent nodes by default. However, the `ParentConnectingVisitor` can be used to achieve this:

```php
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;

$code = '...';

$traverser = new NodeTraverser;
$traverser->addVisitor(new ParentConnectingVisitor);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$ast    = $parser->parse($code);
$ast    = $traverser->traverse($ast);
```

After running this visitor, the parent node can be obtained through `$node->getAttribute('parent')`.

How can the next/previous sibling of a node be obtained?
-----

Again, siblings are not stored by default, but the `NodeConnectingVisitor` can be used to store
the previous / next node with a common parent as well:

```php
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;

$code = '...';

$traverser = new NodeTraverser;
$traverser->addVisitor(new NodeConnectingVisitor);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$ast    = $parser->parse($code);
$ast    = $traverser->traverse($ast);
```

After running this visitor, the parent node can be obtained through `$node->getAttribute('parent')`,
the previous node can be obtained through `$node->getAttribute('previous')`, and the next node can be
obtained through `$node->getAttribute('next')`.

`ParentConnectingVisitor` and `NodeConnectingVisitor` should not be used at the same time. The latter
includes the functionality of the former.
