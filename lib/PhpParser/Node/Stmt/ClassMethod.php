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
            $subNodes + array(
                'type'   => Class_::MODIFIER_PUBLIC,
                'byRef'  => false,
                'params' => array(),
                'stmts'  => array(),
            ),
            $attributes
        );
        $this->name = $name;

        if (($this->type & Class_::MODIFIER_STATIC)
            && ('__construct' == $this->name || '__destruct' == $this->name || '__clone' == $this->name)
        ) {
            throw new Error(sprintf('"%s" method cannot be static', $this->name));
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