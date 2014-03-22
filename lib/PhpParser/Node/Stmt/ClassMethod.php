<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property int          $type   Type
 * @property bool         $byRef  Whether to return by reference
 * @property string       $name   Name
 * @property Node\Param[] $params Parameters
 * @property Node[]       $stmts  Statements
 */
class ClassMethod extends Node\Stmt
{

    /**
     * Constructs a class method node.
     *
     * @param string      $name       Name
     * @param array       $subNodes   Array of the following optional subnodes:
     *                                'type'   => MODIFIER_PUBLIC: Type
     *                                'byRef'  => false          : Whether to return by reference
     *                                'params' => array()        : Parameters
     *                                'stmts'  => array()        : Statements
     * @param array       $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'type'   => isset($subNodes['type'])   ? $subNodes['type']   : Class_::MODIFIER_PUBLIC,
                'byRef'  => isset($subNodes['byRef'])  ? $subNodes['byRef']  : false,
                'name'   => $name,
                'params' => isset($subNodes['params']) ? $subNodes['params'] : array(),
                'stmts'  => array_key_exists('stmts', $subNodes) ? $subNodes['stmts'] : array(),
            ),
            $attributes
        );

        if ($this->type & Class_::MODIFIER_STATIC) {
            switch (strtolower($this->name)) {
                case '__construct':
                    throw new Error(sprintf('Constructor %s() cannot be static', $this->name));
                case '__destruct':
                    throw new Error(sprintf('Destructor %s() cannot be static', $this->name));
                case '__clone':
                    throw new Error(sprintf('Clone method %s() cannot be static', $this->name));
            }
        }
    }

    public function isPublic() {
        return (bool) ($this->type & Class_::MODIFIER_PUBLIC);
    }

    public function isProtected() {
        return (bool) ($this->type & Class_::MODIFIER_PROTECTED);
    }

    public function isPrivate() {
        return (bool) ($this->type & Class_::MODIFIER_PRIVATE);
    }

    public function isAbstract() {
        return (bool) ($this->type & Class_::MODIFIER_ABSTRACT);
    }

    public function isFinal() {
        return (bool) ($this->type & Class_::MODIFIER_FINAL);
    }

    public function isStatic() {
        return (bool) ($this->type & Class_::MODIFIER_STATIC);
    }
}
