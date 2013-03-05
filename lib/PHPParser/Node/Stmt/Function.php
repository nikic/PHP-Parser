<?php

/**
 * @property bool                   $byRef          Whether returns by reference
 * @property string                 $name           Name
 * @property string                 $namespacedName Full qualified function name
 * @property PHPParser_Node_Param[] $params         Parameters
 * @property PHPParser_Node[]       $stmts          Statements
 */
class PHPParser_Node_Stmt_Function extends PHPParser_Node_Stmt
{
    /**
     * Constructs a function node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'byRef'  => false  : Whether to return by reference
     *                           'params' => array(): Parameters
     *                           'stmts'  => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            $subNodes + array(
                'byRef'  => false,
                'params' => array(),
                'stmts'  => array(),
            ),
            $attributes
        );
        $this->name = $name;
    }
}
