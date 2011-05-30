<?php

class NodeDumper
{
    /**
     * Dumps a Node, scalar or array to a string.
     *
     * @param  mixed $node Value to dump
     * @return string Dumped value
     */
    public function dump($node) {
        if (is_array($node) || $node instanceof NodeAbstract) {
            if (is_array($node)) {
                $r = 'array(';
            } else {
                $r = $node->getType() . '(';
            }

            foreach ($node as $key => $value) {
                $r .= "\n" . '    ' . $key . ': ';

                $lines = explode("\n", $this->dump($value));

                $r .= array_shift($lines);
                foreach ($lines as $line) {
                    $r .= "\n" . '    ' . $line;
                }
            }

            return $r . "\n" . ')';
        } elseif (null === $node) {
            return 'null';
        } elseif (false === $node) {
            return 'false';
        } elseif (true === $node) {
            return 'true';
        } elseif (is_scalar($node)) {
            return $node;
        } else {
            throw new InvalidArgumentException('Unexpected node type.');
        }
    }
}