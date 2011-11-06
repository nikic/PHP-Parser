<?php

class PHPParser_NodeVisitor_NameResolver extends PHPParser_NodeVisitorAbstract
{
    /**
     * @var null|PHPParser_Node_Name Current namespace
     */
    protected $namespace;

    /**
     * @var array Currently defined namespace and class aliases
     */
    protected $aliases;

    public function beforeTraverse(array $nodes) {
        $this->namespace = null;
        $this->aliases   = array();
    }

    public function enterNode(PHPParser_Node $node) {
        if ($node instanceof PHPParser_Node_Stmt_Namespace) {
            $this->namespace = $node->name;
            $this->aliases   = array();
        } elseif ($node instanceof PHPParser_Node_Stmt_UseUse) {
            if (isset($this->aliases[$node->alias])) {
                throw new PHPParser_Error(
                    sprintf(
                        'Cannot use %s as %s because the name is already in use',
                        $node->name, $node->alias
                    ),
                    $node->getLine()
                );
            }

            $this->aliases[$node->alias] = $node->name;
        } elseif ($node instanceof PHPParser_Node_Stmt_Class) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }
        } elseif ($node instanceof PHPParser_Node_Expr_StaticCall
                  || $node instanceof PHPParser_Node_Expr_StaticPropertyFetch
                  || $node instanceof PHPParser_Node_Expr_ClassConstFetch
                  || $node instanceof PHPParser_Node_Expr_New
                  || $node instanceof PHPParser_Node_Expr_Instanceof
        ) {
            $node->class = $this->resolveClassName($node->class);
        } elseif ($node instanceof PHPParser_Node_Expr_FuncCall
                  || $node instanceof PHPParser_Node_Expr_ConstFetch
        ) {
            $node->name = $this->resolveOtherName($node->name);
        } elseif ($node instanceof PHPParser_Node_Param) {
            if (!is_null($node->type))
            {
                $node->type = $this->resolveClassName($node->type);
            }            
        }
    }

    protected function resolveClassName(PHPParser_Node $name) {
        // can't resolve dynamic names at compile-time
        if (!$name instanceof PHPParser_Node_Name) {
            return $name;
        }

        // don't resolve special class names
        if (in_array((string) $name, array('self', 'parent', 'static'))) {
            return $name;
        }

        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases (for non-relative names)
        if (!$name->isRelative() && isset($this->aliases[$name->getFirst()])) {
            $name->setFirst($this->aliases[$name->getFirst()]);
        // if no alias exists prepend current namespace
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new PHPParser_Node_Name_FullyQualified($name->parts);
    }

    protected function resolveOtherName(PHPParser_Node $name) {
        // can't resolve dynamic names at compile-time
        if (!$name instanceof PHPParser_Node_Name) {
            return $name;
        }

        // fully qualified names are already resolved and we can't do anything about unqualified
        // ones at compiler-time
        if ($name->isFullyQualified() || $name->isUnqualified()) {
            return $name;
        }

        // resolve aliases for qualified names
        if ($name->isQualified() && isset($this->aliases[$name->getFirst()])) {
            $name->setFirst($this->aliases[$name->getFirst()]);
        // prepend namespace for relative names
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new PHPParser_Node_Name_FullyQualified($name->parts);
    }
}