<?php

/**
 * @property PHPParser_Node_Name $name  Namespace/Class to alias
 * @property string              $alias Alias
 */
class PHPParser_Node_Stmt_UseUse extends PHPParser_Node_Stmt
{
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, $line, $docComment);

        if (null === $this->alias) {
            $this->alias = $this->name->getLast();
        }

        if ('self' == $this->alias || 'parent' == $this->alias) {
            throw new PHPParser_Error(sprintf(
                'Cannot use "%s" as "%s" because "%2$s" is a special class name',
                $this->name, $this->alias
            ));
        }
    }
}