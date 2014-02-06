<?php

namespace PhpParser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class NameResolver extends NodeVisitorAbstract
{
    /**
     * @var null|Name Current namespace
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

    public function enterNode(Node $node) {
        if ($node instanceof Stmt\Namespace_) {
            $this->namespace = $node->name;
            $this->aliases   = array();
        } elseif ($node instanceof Stmt\UseUse) {
            $aliasName = strtolower($node->alias);
            if (isset($this->aliases[$aliasName])) {
                throw new Error(
                    sprintf(
                        'Cannot use "%s" as "%s" because the name is already in use',
                        $node->name, $node->alias
                    ),
                    $node->getLine()
                );
            }

            $this->aliases[$aliasName] = $node->name;
        } elseif ($node instanceof Stmt\Class_) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Trait_) {
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Function_) {
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Const_) {
            foreach ($node->consts as $const) {
                $this->addNamespacedName($const);
            }
        } elseif ($node instanceof Expr\StaticCall
                  || $node instanceof Expr\StaticPropertyFetch
                  || $node instanceof Expr\ClassConstFetch
                  || $node instanceof Expr\New_
                  || $node instanceof Expr\Instanceof_
        ) {
            if ($node->class instanceof Name) {
                $node->class = $this->resolveClassName($node->class);
            }
        } elseif ($node instanceof Stmt\Catch_) {
            $node->type = $this->resolveClassName($node->type);
        } elseif ($node instanceof Expr\FuncCall
                  || $node instanceof Expr\ConstFetch
        ) {
            if ($node->name instanceof Name) {
                $node->name = $this->resolveOtherName($node->name);
            }
        } elseif ($node instanceof Stmt\TraitUse) {
            foreach ($node->traits as &$trait) {
                $trait = $this->resolveClassName($trait);
            }
        } elseif ($node instanceof Node\Param
                  && $node->type instanceof Name
        ) {
            $node->type = $this->resolveClassName($node->type);
        }
    }

    protected function resolveClassName(Name $name) {
        // don't resolve special class names
        if (in_array((string) $name, array('self', 'parent', 'static'))) {
            return $name;
        }

        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases (for non-relative names)
        $aliasName = strtolower($name->getFirst());
        if (!$name->isRelative() && isset($this->aliases[$aliasName])) {
            $name->setFirst($this->aliases[$aliasName]);
        // if no alias exists prepend current namespace
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new Name\FullyQualified($name->parts, $name->getAttributes());
    }

    protected function resolveOtherName(Name $name) {
        // fully qualified names are already resolved and we can't do anything about unqualified
        // ones at compiler-time
        if ($name->isFullyQualified() || $name->isUnqualified()) {
            return $name;
        }

        // resolve aliases for qualified names
        $aliasName = strtolower($name->getFirst());
        if ($name->isQualified() && isset($this->aliases[$aliasName])) {
            $name->setFirst($this->aliases[$aliasName]);
        // prepend namespace for relative names
        } elseif (null !== $this->namespace) {
            $name->prepend($this->namespace);
        }

        return new Name\FullyQualified($name->parts, $name->getAttributes());
    }

    protected function addNamespacedName(Node $node) {
        if (null !== $this->namespace) {
            $node->namespacedName = clone $this->namespace;
            $node->namespacedName->append($node->name);
        } else {
            $node->namespacedName = new Name($node->name, $node->getAttributes());
        }
    }
}
