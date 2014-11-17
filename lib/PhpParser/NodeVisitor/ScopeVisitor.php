<?php

namespace PhpParser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class ScopeVisitor extends NodeVisitorAbstract {
	const SCOPE_SEPARATOR = '::';
	const CLOSURE_NAME = '{closure}';

	/**
	 * @var array The scope stack for the current node
	 */
	protected $scope = array();

	public function enterNode( Node $node ) {
		$node->setAttribute(
			'scope',
			implode( self::SCOPE_SEPARATOR, $this->scope )
		);

		if (
			$node instanceof Stmt\NameSpace_ ||
			$node instanceof Stmt\Class_ ||
			$node instanceof Stmt\ClassMethod ||
			$node instanceof Stmt\Function_
		) {
			$this->scope[] = $node->name;
		} elseif ( $node instanceof Expr\Closure ) {
			$this->scope[] = self::CLOSURE_NAME;
		}
		return $node;
	}

	public function leaveNode( Node $node ) {
		if (
			$node instanceof Stmt\NameSpace_ ||
			$node instanceof Stmt\Class_ ||
			$node instanceof Stmt\ClassMethod ||
			$node instanceof Stmt\Function_ ||
			$node instanceof Expr\Closure
		) {
			array_pop( $this->scope );
		}
	}
}
