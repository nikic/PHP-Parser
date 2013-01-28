<?php

/**
 * @property PHPParser_Node_Expr $left	The left hand side expression
 * @property PHPParser_Node_Expr $right The right hand side expression
 */
class PHPParser_Node_Expr_ShiftRight extends PHPParser_Node_Expr
{
		/**
		 * Constructs a shift right node.
		 *
		 * @param PHPParser_Node_Expr $left			 The left hand side expression
		 * @param PHPParser_Node_Expr $right			The right hand side expression
		 * @param array							 $attributes Additional attributes
		 */
		public function __construct(PHPParser_Node_Expr $left, PHPParser_Node_Expr $right, array $attributes = array()) {
				parent::__construct(
						array(
								'left'	=> $left,
								'right' => $right
						),
						$attributes
				);
		}
}