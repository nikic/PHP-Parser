<?php

abstract class NodeAbstract implements IteratorAggregate
{
    protected $subNodes = array();

    public function __construct(array $subNodes) {
        $this->subNodes = $subNodes;
    }

    public function __get($name) {
        if (!isset($this->subNodes[$name])) {
            throw new OutOfBoundsException();
        }

        return $this->subNodes[$name];
    }

    public function getIterator() {
        return new ArrayIterator($this->subNodes);
    }

    public function __toString() {
        $r = '[' . substr(get_class($this), 5) . ']';

        foreach ($this->subNodes as $key => $value) {
            $r .= "\n" . '    ' . $key . ': ';

            if (null === $value) {
                $r .= 'null';
            } elseif (false === $value) {
                $r .= 'false';
            } elseif (true === $value) {
                $r .= 'true';
            } elseif (is_scalar($value)) {
                $r .= $value;
            } elseif ($value instanceof NodeAbstract) {
                $lines = explode("\n", $value);
                $r .= array_shift($lines);
                foreach ($lines as $line) {
                    $r .= "\n" . '    ' . $line;
                }
            } elseif (is_array($value)) {
                foreach ($value as $v) {
                    $lines = explode("\n", $v);
                    foreach ($lines as $line) {
                        $r .= "\n" . '        ' . $line;
                    }
                }
            } else {
                die('UNEXPECTED!!!');
            }
        }

        return $r;
    }
}