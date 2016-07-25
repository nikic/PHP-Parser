<?php

namespace PhpParser;

use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class NodeDumper
{
    private $dumpComments;

    /**
     * Constructs a NodeDumper.
     *
     * @param array $options Boolean option 'dumpComments' controls whether comments should be
     *                       dumped
     */
    public function __construct(array $options = []) {
        $this->dumpComments = !empty($options['dumpComments']);
    }

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

            foreach ($node->getSubNodeNames() as $key) {
                $r .= "\n    " . $key . ': ';

                $value = $node->$key;
                if (null === $value) {
                    $r .= 'null';
                } elseif (false === $value) {
                    $r .= 'false';
                } elseif (true === $value) {
                    $r .= 'true';
                } elseif (is_scalar($value)) {
                    if ('flags' === $key || 'newModifier' === $key) {
                        $r .= $this->dumpFlags($value);
                    } else if ('type' === $key && $node instanceof Include_) {
                        $r .= $this->dumpIncludeType($value);
                    } else if ('type' === $key
                            && ($node instanceof Use_ || $node instanceof UseUse || $node instanceof GroupUse)) {
                        $r .= $this->dumpUseType($value);
                    } else {
                        $r .= $value;
                    }
                } else {
                    $r .= str_replace("\n", "\n    ", $this->dump($value));
                }
            }

            if ($this->dumpComments && $comments = $node->getAttribute('comments')) {
                $r .= "\n    comments: " . str_replace("\n", "\n    ", $this->dump($comments));
            }
        } elseif (is_array($node)) {
            $r = 'array(';

            foreach ($node as $key => $value) {
                $r .= "\n    " . $key . ': ';

                if (null === $value) {
                    $r .= 'null';
                } elseif (false === $value) {
                    $r .= 'false';
                } elseif (true === $value) {
                    $r .= 'true';
                } elseif (is_scalar($value)) {
                    $r .= $value;
                } else {
                    $r .= str_replace("\n", "\n    ", $this->dump($value));
                }
            }
        } elseif ($node instanceof Comment) {
            return $node->getReformattedText();
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }

        return $r . "\n)";
    }

    protected function dumpFlags($flags) {
        $strs = [];
        if ($flags & Class_::MODIFIER_PUBLIC) {
            $strs[] = 'MODIFIER_PUBLIC';
        }
        if ($flags & Class_::MODIFIER_PROTECTED) {
            $strs[] = 'MODIFIER_PROTECTED';
        }
        if ($flags & Class_::MODIFIER_PRIVATE) {
            $strs[] = 'MODIFIER_PRIVATE';
        }
        if ($flags & Class_::MODIFIER_ABSTRACT) {
            $strs[] = 'MODIFIER_ABSTRACT';
        }
        if ($flags & Class_::MODIFIER_STATIC) {
            $strs[] = 'MODIFIER_STATIC';
        }
        if ($flags & Class_::MODIFIER_FINAL) {
            $strs[] = 'MODIFIER_FINAL';
        }

        if ($strs) {
            return implode(' | ', $strs) . ' (' . $flags . ')';
        } else {
            return $flags;
        }
    }

    protected function dumpIncludeType($type) {
        $map = [
            Include_::TYPE_INCLUDE      => 'TYPE_INCLUDE',
            Include_::TYPE_INCLUDE_ONCE => 'TYPE_INCLUDE_ONCE',
            Include_::TYPE_REQUIRE      => 'TYPE_REQUIRE',
            Include_::TYPE_REQUIRE_ONCE => 'TYPE_REQURE_ONCE',
        ];

        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type] . ' (' . $type . ')';
    }

    protected function dumpUseType($type) {
        $map = [
            Use_::TYPE_UNKNOWN  => 'TYPE_UNKNOWN',
            Use_::TYPE_NORMAL   => 'TYPE_NORMAL',
            Use_::TYPE_FUNCTION => 'TYPE_FUNCTION',
            Use_::TYPE_CONSTANT => 'TYPE_CONSTANT',
        ];

        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type] . ' (' . $type . ')';
    }
}
