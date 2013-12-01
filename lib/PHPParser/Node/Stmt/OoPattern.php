<?php

/**
 * PHPParser_Node_Stmt_ObjectDefinition is a mother class for all
 * object oriented programing definitions.
 */
abstract class PHPParser_Node_Stmt_OoPattern extends PHPParser_Node_Stmt
{

    protected static $specialNames = array(
        'self' => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'name' => Name of the class/interface/trait
     *                           'stmts'   => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $subNodes = array(), array $attributes = array()) 
    {
        parent::__construct($subNodes, $attributes);

        if (isset(self::$specialNames[(string) $this->name])) {
            $typeDef = array();
            preg_match('#_([^_]+)$#', get_class(), $typeDef);
            throw new PHPParser_Error(sprintf('Cannot use "%s" as %s name as it is reserved', $this->name, strtolower($typeDef[1])));
        }
    }

    public function getMethods()
    {
        $methods = array();
        foreach ($this->stmts as $stmt) {
            if ($stmt instanceof PHPParser_Node_Stmt_ClassMethod) {
                $methods[] = $stmt;
            }
        }
        return $methods;
    }

}