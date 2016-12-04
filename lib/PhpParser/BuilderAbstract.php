<?php

namespace PhpParser;

use PhpParser\Comment;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

abstract class BuilderAbstract implements Builder {
    /**
     * Normalizes a node: Converts builder objects to nodes.
     *
     * @param Node|Builder $node The node to normalize
     *
     * @return Node The normalized node
     */
    protected function normalizeNode($node) {
        if ($node instanceof Builder) {
            return $node->getNode();
        } elseif ($node instanceof Node) {
            return $node;
        }

        throw new \LogicException('Expected node or builder object');
    }

    /**
     * Normalizes a name: Converts plain string names to PhpParser\Node\Name.
     *
     * @param Name|string $name The name to normalize
     *
     * @return Name The normalized name
     */
    protected function normalizeName($name) {
        if ($name instanceof Name) {
            return $name;
        } elseif (is_string($name)) {
            if (!$name) {
                throw new \LogicException('Name cannot be empty');
            }

            if ($name[0] == '\\') {
                return new Name\FullyQualified(substr($name, 1));
            } elseif (0 === strpos($name, 'namespace\\')) {
                return new Name\Relative(substr($name, strlen('namespace\\')));
            } else {
                return new Name($name);
            }
        }

        throw new \LogicException('Name must be a string or an instance of PhpParser\Node\Name');
    }

    /**
     * Normalizes a type: Converts plain-text type names into proper AST representation.
     *
     * In particular, builtin types are left as strings, custom types become Names and nullables
     * are wrapped in NullableType nodes.
     *
     * @param Name|string|NullableType $type The type to normalize
     *
     * @return Name|string|NullableType The normalized type
     */
    protected function normalizeType($type) {
        if (!is_string($type)) {
            if (!$type instanceof Name && !$type instanceof NullableType) {
                throw new \LogicException(
                    'Type must be a string, or an instance of Name or NullableType');
            }
            return $type;
        }

        $nullable = false;
        if (strlen($type) > 0 && $type[0] === '?') {
            $nullable = true;
            $type = substr($type, 1);
        }

        $builtinTypes = array(
            'array', 'callable', 'string', 'int', 'float', 'bool', 'iterable', 'void'
        );

        $lowerType = strtolower($type);
        if (in_array($lowerType, $builtinTypes)) {
            $type = $lowerType;
        } else {
            $type = $this->normalizeName($type);
        }

        if ($nullable && $type === 'void') {
            throw new \LogicException('void type cannot be nullable');
        }

        return $nullable ? new Node\NullableType($type) : $type;
    }

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return Expr The normalized value
     */
    protected function normalizeValue($value) {
        if ($value instanceof Node) {
            return $value;
        } elseif (is_null($value)) {
            return new Expr\ConstFetch(
                new Name('null')
            );
        } elseif (is_bool($value)) {
            return new Expr\ConstFetch(
                new Name($value ? 'true' : 'false')
            );
        } elseif (is_int($value)) {
            return new Scalar\LNumber($value);
        } elseif (is_float($value)) {
            return new Scalar\DNumber($value);
        } elseif (is_string($value)) {
            return new Scalar\String_($value);
        } elseif (is_array($value)) {
            $items = array();
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new Expr\ArrayItem(
                        $this->normalizeValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new Expr\ArrayItem(
                        $this->normalizeValue($itemValue),
                        $this->normalizeValue($itemKey)
                    );
                }
            }

            return new Expr\Array_($items);
        } else {
            throw new \LogicException('Invalid value');
        }
    }

    /**
     * Normalizes a doc comment: Converts plain strings to PhpParser\Comment\Doc.
     *
     * @param Comment\Doc|string $docComment The doc comment to normalize
     *
     * @return Comment\Doc The normalized doc comment
     */
    protected function normalizeDocComment($docComment) {
        if ($docComment instanceof Comment\Doc) {
            return $docComment;
        } else if (is_string($docComment)) {
            return new Comment\Doc($docComment);
        } else {
            throw new \LogicException('Doc comment must be a string or an instance of PhpParser\Comment\Doc');
        }
    }

    /**
     * Sets a modifier in the $this->type property.
     *
     * @param int $modifier Modifier to set
     */
    protected function setModifier($modifier) {
        Stmt\Class_::verifyModifier($this->flags, $modifier);
        $this->flags |= $modifier;
    }
}
