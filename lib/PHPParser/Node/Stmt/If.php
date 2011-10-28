<?php

/**
 * @property PHPParser_Node_Expr           $cond    Condition expression
 * @property PHPParser_Node[]              $stmts   Statements
 * @property PHPParser_Node_Stmt_ElseIf[]  $elseifs Elseif clauses
 * @property null|PHPParser_Node_Stmt_Else $else    Else clause
 */
class PHPParser_Node_Stmt_If extends PHPParser_Node_Stmt
{

    /**
     * Constructs an if node.
     *
     * @param PHPParser_Node_Expr $cond       Condition
     * @param array               $subNodes   Array of the following optional subnodes:
     *                                        'stmts'   => array(): Statements
     *                                        'elseifs' => array(): Elseif clauses
     *                                        'else'    => null   : Else clause
     * @param int                 $line       Line
     * @param null|string         $docComment Nearest doc comment
     */
    public function __construct(PHPParser_Node_Expr $cond, array $subNodes = array(), $line = -1, $docComment = null) {
        parent::__construct(
            $subNodes + array(
                'stmts'   => array(),
                'elseifs' => array(),
                'else'    => null,
            ),
            $line, $docComment
        );
        $this->cond = $cond;
    }
}