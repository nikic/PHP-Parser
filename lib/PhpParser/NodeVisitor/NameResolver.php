<?php

namespace PhpParser\NodeVisitor;

use PhpParser\Error;
use PhpParser\ErrorHandler;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

class NameResolver extends NodeVisitorAbstract
{
    /** @var null|Name Current namespace */
    protected $namespace;

    /** @var array Map of format [aliasType => [aliasName => originalName]] */
    protected $aliases;

    /** @var ErrorHandler Error handler */
    protected $errorHandler;

    /**
     * Constructs a name resolution visitor.
     *
     * @param ErrorHandler|null $errorHandler Error handler
     */
    public function __construct(ErrorHandler $errorHandler = null) {
        $this->errorHandler = $errorHandler ?: new ErrorHandler\Throwing;
    }

    public function beforeTraverse(array $nodes) {
        $this->resetState();
    }

    public function enterNode(Node $node) {
        if ($node instanceof Stmt\Namespace_) {
            $this->resetState($node->name);
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, null);
            }
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, $node->prefix);
            }
        } elseif ($node instanceof Stmt\Class_) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            if (null !== $node->name) {
                $this->addNamespacedName($node);
            }
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }

            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Trait_) {
            $this->addNamespacedName($node);
        } elseif ($node instanceof Stmt\Function_) {
            $this->addNamespacedName($node);
            $this->resolveSignature($node);
        } elseif ($node instanceof Stmt\ClassMethod
                  || $node instanceof Expr\Closure
        ) {
            $this->resolveSignature($node);
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
            foreach ($node->types as &$type) {
                $type = $this->resolveClassName($type);
            }
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

            foreach ($node->adaptations as $adaptation) {
                if (null !== $adaptation->trait) {
                    $adaptation->trait = $this->resolveClassName($adaptation->trait);
                }

                if ($adaptation instanceof Stmt\TraitUseAdaptation\Precedence) {
                    foreach ($adaptation->insteadof as &$insteadof) {
                        $insteadof = $this->resolveClassName($insteadof);
                    }
                }
            }
        } elseif ($node instanceof Node\NullableType) {
            if ($node->type instanceof Name) {
                $node->type = $this->resolveClassName($node->type);
            }
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

    protected function addAlias(Stmt\UseUse $use, $type, Name $prefix = null) {
        // Add prefix for group uses
        $name = $prefix ? Name::concat($prefix, $use->name) : $use->name;
        // Type is determined either by individual element or whole use declaration
        $type |= $use->type;

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

            $this->errorHandler->handleError(new Error(
                sprintf(
                    'Cannot use %s%s as %s because the name is already in use',
                    $typeStringMap[$type], $name, $use->alias
                ),
                $use->getAttributes()
            ));
            return;
        }

        $this->aliases[$type][$aliasName] = $name;
    }

    /** @param Stmt\Function_|Stmt\ClassMethod|Expr\Closure $node */
    private function resolveSignature($node) {
        foreach ($node->params as $param) {
            if ($param->type instanceof Name) {
                $param->type = $this->resolveClassName($param->type);
            }
        }
        if ($node->returnType instanceof Name) {
            $node->returnType = $this->resolveClassName($node->returnType);
        }
    }

    protected function resolveClassName(Name $name) {
        // don't resolve special class names
        if (in_array(strtolower($name->toString()), array('self', 'parent', 'static'))) {
            if (!$name->isUnqualified()) {
                $this->errorHandler->handleError(new Error(
                    sprintf("'\\%s' is an invalid class name", $name->toString()),
                    $name->getAttributes()
                ));
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
            $alias = $this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName];
            return FullyQualified::concat($alias, $name->slice(1), $name->getAttributes());
        }

        // if no alias exists prepend current namespace
        return FullyQualified::concat($this->namespace, $name, $name->getAttributes());
    }

    protected function resolveOtherName(Name $name, $type) {
        // fully qualified names are already resolved
        if ($name->isFullyQualified()) {
            return $name;
        }

        // resolve aliases for qualified names
        $aliasName = strtolower($name->getFirst());
        if ($name->isQualified() && isset($this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName])) {
            $alias = $this->aliases[Stmt\Use_::TYPE_NORMAL][$aliasName];
            return FullyQualified::concat($alias, $name->slice(1), $name->getAttributes());
        }

        if ($name->isUnqualified()) {
            if ($type === Stmt\Use_::TYPE_CONSTANT) {
                // constant aliases are case-sensitive, function aliases case-insensitive
                $aliasName = $name->getFirst();
            }

            if (isset($this->aliases[$type][$aliasName])) {
                // resolve unqualified aliases
                return new FullyQualified($this->aliases[$type][$aliasName], $name->getAttributes());
            }

            if (null === $this->namespace) {
                // outside of a namespace unaliased unqualified is same as fully qualified
                return new FullyQualified($name, $name->getAttributes());
            }

            // unqualified names inside a namespace cannot be resolved at compile-time
            // add the namespaced version of the name as an attribute
            $name->setAttribute('namespacedName',
                FullyQualified::concat($this->namespace, $name, $name->getAttributes()));
            return $name;
        }

        // if no alias exists prepend current namespace
        return FullyQualified::concat($this->namespace, $name, $name->getAttributes());
    }

    protected function addNamespacedName(Node $node) {
        $node->namespacedName = Name::concat($this->namespace, $node->name);
    }
}
