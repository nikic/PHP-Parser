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
     * @var array Map of format [aliasType => [aliasName => originalName]]
     */
    protected $aliases;

    public function beforeTraverse(array $nodes) {
        $this->resetState();
    }

    public function enterNode(Node $node) {
        if ($node instanceof Stmt\Namespace_) {
            $this->resetState($node->name);
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type);
            }
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
        } elseif ($node instanceof Expr\FuncCall) {
            if ($node->name instanceof Name) {
                $node->name = $this->resolveOtherName($node->name, Stmt\Use_::TYPE_FUNCTION);
            }
        } elseif ($node instanceof Expr\ConstFetch) {
            $node->name = $this->resolveOtherName($node->name, Stmt\Use_::TYPE_CONSTANT);
        } elseif ($node instanceof Stmt\TraitUse) {
            foreach ($node->traits as &$trait) {
                $trait = $this->resolveClassName($trait);
            }

            foreach($node->adaptations as $adaptation) {
                if (null !== $adaptation->trait) {
                    $adaptation->trait = $this->resolveClassName($adaptation->trait);
                }

                if ($adaptation instanceof Stmt\TraitUseAdaptation\Precedence) {
                    foreach ($adaptation->insteadof as &$insteadof) {
                        $insteadof = $this->resolveClassName($insteadof);
                    }
                }
            }

        } elseif ($node instanceof Node\Param
                  && $node->type instanceof Name
        ) {
            $node->type = $this->resolveClassName($node->type);
        }
    }

    protected function resetState(Name $namespace = null) {
        $this->namespace = $namespace;
        $this->aliases   = array(
            Stmt\Use_::TYPE_NORMAL   => array(),
            Stmt\Use_::TYPE_FUNCTION => array(),
            Stmt\Use_::TYPE_CONSTANT => array(),
        );
    }

    protected function addAlias(Stmt\UseUse $use, $type) {
        // Constant names are case sensitive, everything else case insensitive
        if ($type === Stmt\Use_::TYPE_CONSTANT) {
            $aliasName = $use->alias;
        } else {
            $aliasName = strtolower($use->alias);
        }

        if (isset($this->aliases[$type][$aliasName])) {
            $typeStringMap = array(
                Stmt\Use_::TYPE_NORMAL   => '',
                Stmt\Use_::TYPE_FUNCTION => 'function ',
                Stmt\Use_::TYPE_CONSTANT => 'const ',
            );

            throw new Error(
                sprintf(
                    'Cannot use %s%s as %s because the name is already in use',
                    $typeStringMap[$type], $use->name, $use->alias
                ),
                $use->getLine()
            );
        }

        $this->aliases[$type][$aliasName] = $use->name;
    }

    protected function resolveClassName(Name $name) {
        // don't resolve special class names
        if (in_array(strtolower($name), array('self', 'parent', 'static'))) {
            if (!$name->isUnqualified()) {
                throw new Error(
                    sprintf("'\\%s' is an invalid class name", $name->toString()),
                    $name->getLine()
                );
            }

            return $name;
        }

        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        $aliasName = strtolower($name->getFirst());
        if (!$name->isRelative() && isset($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName])) {
            // resolve aliases (for non-relative names)
            $name->setFirst($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName]);
        } elseif (null !== $this->namespace) {
            // if no alias exists prepend current namespace
            $name->prepend($this->namespace);
        }

        return new Name\FullyQualified($name->parts, $name->getAttributes());
    }

    protected function resolveOtherName(Name $name, $type) {
        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases for qualified names
        $aliasName = strtolower($name->getFirst());
        if ($name->isQualified() && isset($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName])) {
            $name->setFirst($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName]);
        } elseif ($name->isUnqualified()) {
            if ($type === Stmt\Use_::TYPE_CONSTANT) {
                // constant aliases are case-sensitive, function aliases case-insensitive
                $aliasName = $name->getFirst();
            }

            if (isset($this->aliases[$type][$aliasName])) {
                // resolve unqualified aliases
                $name->set($this->aliases[$type][$aliasName]);
            } else {
                // unqualified, unaliased names cannot be resolved at compile-time
                return $name;
            }
        } elseif (null !== $this->namespace) {
            // if no alias exists prepend current namespace
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
