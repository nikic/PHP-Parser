<?php

/**
 * @property PHPParser_Node_Expr $expr Expression
 * @property int       $type Type of include
 */
class PHPParser_Node_Expr_Include extends PHPParser_Node_Expr
{
    const TYPE_INCLUDE      = 1;
    const TYPE_INCLUDE_ONCE = 2;
    const TYPE_REQUIRE      = 3;
    const TYPE_REQUIRE_ONCE = 4;
}