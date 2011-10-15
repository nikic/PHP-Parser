<?php

/**
 * @property PHPParser_Node_Stmt_UseUse[] $uses Aliases
 */
class PHPParser_Node_Stmt_Use extends PHPParser_Node_Stmt
{
    /**
     * Constructs an alias (use) list node.
     *
     * @param PHPParser_Node_Stmt_UseUse[] $uses       Aliases
     * @param int                          $line       Line
     * @param null|string                  $docComment Nearest doc comment
     */
    public function __construct(array $uses, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'uses' => $uses,
            ),
            $line, $docComment
        );
    }
}