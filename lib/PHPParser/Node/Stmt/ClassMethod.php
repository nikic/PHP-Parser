<?php

/**
 * @property int                    $type   Type
 * @property bool                   $byRef  Whether to return by reference
 * @property string                 $name   Name
 * @property PHPParser_Node_Param[] $params Parameters
 * @property PHPParser_Node[]       $stmts  Statements
 */
class PHPParser_Node_Stmt_ClassMethod extends PHPParser_Node_Stmt
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
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct($name, array $subNodes = array(), $line = -1, $docComment = null) {
        parent::__construct(
            $subNodes + array(
                'type'   => PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC,
                'byRef'  => false,
                'params' => array(),
                'stmts'  => array(),
            ),
            $line, $docComment
        );
        $this->name = $name;

        if (($this->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC)
            && ('__construct' == $this->name || '__destruct' == $this->name || '__clone' == $this->name)
        ) {
            throw new PHPParser_Error(sprintf('"%s" method cannot be static', $this->name));
        }
    }
}