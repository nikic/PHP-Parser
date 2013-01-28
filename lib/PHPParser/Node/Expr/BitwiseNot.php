<?php

/**
 * @property PHPParser_Node_Expr $expr Expression
 */
class PHPParser_Node_Expr_BitwiseNot extends PHPParser_Node_Expr
{
		/**
		 * Constructs a bitwise not node.
		 *
		 * @param PHPParser_Node_Expr $expr			 Expression
		 * @param array							 $attributes Additional attributes
		 */
		public function __construct(PHPParser_Node_Expr $expr, array $attributes = array()) {
				parent::__construct(
						array(
								'expr' => $expr
						),
						$attributes
				);
		}
}