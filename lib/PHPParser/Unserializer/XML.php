<?php

class PHPParser_Unserializer_XML implements PHPParser_Unserializer
{
    protected $reader;

    public function __construct() {
        $this->reader = new XMLReader;
    }

    public function unserialize($string) {
        $this->reader->XML($string);

        $this->reader->read();
        if ('AST' !== $this->reader->name) {
            throw new Exception('AST root element not found');
        }

        return $this->read();
    }

    protected function read() {
        $depth = $this->reader->depth;
        while ($this->reader->read() && $depth <= $this->reader->depth) {
            if (XMLReader::ELEMENT !== $this->reader->nodeType) {
                continue;
            }

            if ('node' === $this->reader->prefix) {
                $className = 'PHPParser_Node_' . $this->reader->localName;

                // create the node without calling it's constructor
                $node = unserialize(
                    sprintf('O:%d:"%s":0:{}', strlen($className), $className)
                );

                $line = $this->reader->getAttribute('line');
                $node->setLine(null !== $line ? $line : -1);

                $docComment = $this->reader->getAttribute('docComment');
                $node->setDocComment($docComment);

                $depth2 = $this->reader->depth;
                while ($this->reader->read() && $depth2 < $this->reader->depth) {
                    if (XMLReader::ELEMENT !== $this->reader->nodeType) {
                        continue;
                    }

                    if ('subNode' !== $this->reader->prefix) {
                        throw new Exception('Expected sub node');
                    }

                    $subNodeName    = $this->reader->localName;
                    $subNodeContent = $this->read();

                    $node->$subNodeName = $subNodeContent;
                }

                return $node;
            } elseif ('scalar' === $this->reader->prefix) {
                if ('array' === $this->reader->localName) {
                    $array = array();
                    while ($node = $this->read()) {
                        $array[] = $node;
                    }
                    return $array;
                } elseif ('string' === $this->reader->localName) {
                    return $this->readText();
                } elseif ('int' === $this->reader->localName) {
                    return (int) $this->readText();
                } elseif ('float' === $this->reader->localName) {
                    return (float) $this->readText();
                } elseif ('false' === $this->reader->localName
                          || 'true' === $this->reader->localName
                          || 'null' === $this->reader->localName
                ) {
                    if ($this->reader->hasValue) {
                        throw new Exception('false, true and null nodes cannot have a value');
                    }

                    return constant($this->reader->localName);
                } else {
                    throw new Exception('Unexpected scalar type');
                }
            } else {
                throw new Exception('Unexpected node type');
            }
        }
    }

    protected function readText() {
        if (!$this->reader->read() || XMLReader::TEXT !== $this->reader->nodeType) {
            throw new Exception('Expected text node');
        }

        return $this->reader->value;
    }
}
