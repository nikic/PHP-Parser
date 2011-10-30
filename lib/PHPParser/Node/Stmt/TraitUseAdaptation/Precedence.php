<?php

/**
 * @property PHPParser_Node_Name   $trait     Trait name
 * @property string                $method    Method name
 * @property PHPParser_Node_Name[] $insteadof Overwritten traits
 */
class PHPParser_Node_Stmt_TraitUseAdaptation_Precedence extends PHPParser_Node_Stmt_TraitUseAdaptation
{
    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param PHPParser_Node_Name   $trait       Trait name
     * @param string                $method      Method name
     * @param PHPParser_Node_Name[] $insteadof   Overwritten traits
     * @param int                   $line        Line
     * @param null|string           $docComment  Nearest doc comment
     */
    public function __construct(PHPParser_Node_Name $trait, $method, array $insteadof, $line = -1, $docComment = null) {
        parent::__construct(
            array(
                'trait'     => $trait,
                'method'    => $method,
                'insteadof' => $insteadof,
            ),
            $line, $docComment
        );
    }
}