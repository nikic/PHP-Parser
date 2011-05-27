<?php

abstract class PrettyPrinterAbstract
{
    public function pCommaSeparated(array $nodes) {
        $pNodes = array();
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
        }
        return implode(', ', $pNodes);
    }

    public function pStmts(array $nodes) {
        $return = '';
        foreach ($nodes as $node) {
            $return .= $this->p($node);

            if ($node instanceof Node_Stmt_Func) {
                $return .= "\n";
            } else {
                $return .= ';' . "\n";
            }
        }
        return $return;
    }

    public function pIndent($string) {
        $lines = explode("\n", $string);
        foreach ($lines as &$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        }

        return implode("\n", $lines);
    }

    public function p(NodeAbstract $node) {
        if (method_exists($this, 'p' . $node->getType())) {
            return $this->{'p' . $node->getType()}($node);
        } else {
            echo 'Missing: ' . 'p' . $node->getType() . "\n";

            return '';
        }
    }
}