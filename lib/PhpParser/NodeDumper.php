<?php

namespace PhpParser;

class NodeDumper
{
    /**
     * Dumps a node or array.
     *
     * @param array|Node $node Node or array to dump
     *
     * @return string Dumped value
     */
    public function dump($node) {
        if ($node instanceof Node) {
            $r = $node->getType() . '(';
        } elseif (is_array($node)) {
            $r = 'array(';
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }

        foreach ($node as $key => $value) {
            $r .= "\n" . '    ' . $key . ': ';

            if (null === $value) {
                $r .= 'null';
            } elseif (false === $value) {
                $r .= 'false';
            } elseif (true === $value) {
                $r .= 'true';
            } elseif (is_scalar($value)) {
                $r .= $value;
            } else {
                $r .= str_replace("\n", "\n" . '    ', $this->dump($value));
            }
        }

        return $r . "\n" . ')';
    }
}