<?php

/**
 * @property PHPParser_Node_Name $name  Namespace/Class to alias
 * @property string              $alias Alias
 */
class PHPParser_Node_Stmt_UseUse extends PHPParser_Node_Stmt
{
    /**
     * Constructs an alias (use) node.
     *
     * @param PHPParser_Node_Name $name       Namespace/Class to alias
     * @param null|string         $alias      Alias
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Name $name, $alias = null, $line = -1, $docComment = null) {
        if (null === $alias) {
            $alias = $name->getLast();
        }

        if ('self' == $alias || 'parent' == $alias) {
            throw new PHPParser_Error(sprintf(
                'Cannot use "%s" as "%s" because "%2$s" is a special class name',
                $name, $alias
            ));
        }

        parent::__construct(
            array(
                'name'  => $name,
                'alias' => $alias,
            ),
            $line, $docComment
        );
    }
}