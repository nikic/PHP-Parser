<?php

/**
 * @property int    $type   Type
 * @property bool   $byRef  Whether to return by reference
 * @property string $name   Name
 * @property array  $params Parameters
 * @property array  $stmts  Statements
 */
class PHPParser_Node_Stmt_ClassMethod extends PHPParser_Node_Stmt
{
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, $line, $docComment);

        if (($this->type & PHPParser_Node_Stmt_Class::MODIFIER_STATIC)
            && ('__construct' == $this->name || '__destruct' == $this->name || '__clone' == $this->name)
        ) {
            throw new PHPParser_Error(sprintf('"%s" method cannot be static', $this->name));
        }
    }
}