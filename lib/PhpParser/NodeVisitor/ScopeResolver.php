<?php

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

/**
 * Class ScopeResolver
 *
 * @author Michael Yoo <michael@yoo.id.au>
 * @author Bernhard Reiter <ockham@raz.or.at>
 */
class ScopeResolver extends NodeVisitorAbstract
{
    const SCOPE_SEPARATOR = '::';
    const METHOD_SEPARATOR = '->';
    const NAMESPACE_SEPARATOR = '\\';
    const CLOSURE_DEFINITION = 'Closure';
    const FUNCTION_DEFINITION = '()';

    /** @var array The scope stack for the current node w/ root namespace '\' prepended */
    protected $scope = [self::NAMESPACE_SEPARATOR];

    public function enterNode(Node $node)
    {
        $node->setAttribute('scope', implode("", $this->scope));

        if($node instanceof Stmt\Namespace_ and !empty($node->name))
        {
            $this->scope[] = $node->name.self::NAMESPACE_SEPARATOR;
        }
        elseif($node instanceof Stmt\Class_)
        {
            $this->scope[] = $node->name;
        }
        elseif($node instanceof Stmt\Function_)
        {
            $this->scope[] = $node->name.self::FUNCTION_DEFINITION;
        }
        elseif($node instanceof Stmt\ClassMethod)
        {
            $this->scope[] = self::METHOD_SEPARATOR.$node->name.self::FUNCTION_DEFINITION;
        }
        elseif($node instanceof Expr\Closure)
        {
            // Do not prepend '::' if appending namespace
            if($x = array_values($this->scope) and $e = end($x) and substr($e, -1) === "\\")
            {
                $this->scope[] = self::CLOSURE_DEFINITION;
            }
            else
            {
                $this->scope[] = self::SCOPE_SEPARATOR.self::CLOSURE_DEFINITION;
            }
        }

        return $node;
    }

    public function leaveNode(Node $node)
    {
        if(
            // Do not array_pop the root namespace
            ($node instanceof Stmt\NameSpace_ and count($this->scope) > 1)
            or $node instanceof Stmt\Class_
            or $node instanceof Stmt\ClassMethod
            or $node instanceof Stmt\Function_
            or $node instanceof Expr\Closure
        )
        {
            array_pop($this->scope);
        }
    }
}