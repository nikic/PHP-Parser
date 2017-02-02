<?php

/**
 * Connects the nodes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PHPParser_NodeVisitor_NodeConnector extends PHPParser_NodeVisitorAbstract
{
    public function enterNode(PHPParser_Node $node)
    {
        $subNodes = array();
        foreach ($node as $subNode) {
            if ($subNode instanceof PHPParser_Node) {
                $subNodes[] = $subNode;
                continue;
            } else if (!is_array($subNode)) {
                continue;
            }

            $subNodes = array_merge($subNodes, array_values($subNode));
        }

        for ($i=0,$c=count($subNodes); $i<$c; $i++) {
            if (!$subNodes[$i] instanceof PHPParser_Node) {
                continue;
            }

            $subNodes[$i]->setAttribute('parent', $node);

            if ($i > 0) {
                $subNodes[$i]->setAttribute('previous', $subNodes[$i - 1]);
            }
            if ($i + 1 < $c) {
                $subNodes[$i]->setAttribute('next', $subNodes[$i + 1]);
            }
        }
    }
}