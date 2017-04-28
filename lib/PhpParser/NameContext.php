<?php

namespace PhpParser;

use PhpParser\Builder\Use_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;

class NameContext {
    /** @var null|Name Current namespace */
    protected $namespace;

    /** @var array Map of format [aliasType => [aliasName => originalName]] */
    protected $aliases = [];

    /** @var array Same as $aliases but preserving original case */
    protected $origAliases = [];

    /** @var ErrorHandler Error handler */
    protected $errorHandler;

    /**
     * Create a name context.
     *
     * @param ErrorHandler $errorHandler Error handling used to report errors
     */
    public function __construct(ErrorHandler $errorHandler) {
        $this->errorHandler = $errorHandler;
    }

    /**
     * Start a new namespace.
     *
     * This also resets the alias table.
     *
     * @param Name|null $namespace Null is the global namespace
     */
    public function startNamespace(Name $namespace = null) {
        $this->namespace = $namespace;
        $this->origAliases = $this->aliases = [
            Stmt\Use_::TYPE_NORMAL   => [],
            Stmt\Use_::TYPE_FUNCTION => [],
            Stmt\Use_::TYPE_CONSTANT => [],
        ];
    }

    /**
     * Add an alias / import.
     *
     * @param Name   $name        Original name
     * @param string $aliasName   Aliased name
     * @param int    $type        One of Stmt\Use_::TYPE_*
     * @param array  $errorAttrs Attributes to use to report an error
     */
    public function addAlias(Name $name, string $aliasName, int $type, array $errorAttrs = []) {
        // Constant names are case sensitive, everything else case insensitive
        if ($type === Stmt\Use_::TYPE_CONSTANT) {
            $aliasLookupName = $aliasName;
        } else {
            $aliasLookupName = strtolower($aliasName);
        }

        if (isset($this->aliases[$type][$aliasLookupName])) {
            $typeStringMap = array(
                Stmt\Use_::TYPE_NORMAL   => '',
                Stmt\Use_::TYPE_FUNCTION => 'function ',
                Stmt\Use_::TYPE_CONSTANT => 'const ',
            );

            $this->errorHandler->handleError(new Error(
                sprintf(
                    'Cannot use %s%s as %s because the name is already in use',
                    $typeStringMap[$type], $name, $aliasName
                ),
                $errorAttrs
            ));
            return;
        }

        $this->aliases[$type][$aliasLookupName] = $name;
        $this->origAliases[$type][$aliasName] = $name;
    }

    /**
     * Get current namespace.
     *
     * @return null|Name Namespace (or null if global namespace)
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Get resolved name.
     *
     * @param Name $name Name to resolve
     * @param int  $type One of Stmt\Use_::TYPE_{FUNCTION|CONSTANT}
     *
     * @return null|Name Resolved name, or null if static resolution is not possible
     */
    public function getResolvedName(Name $name, int $type) {
        // don't resolve special class names
        if ($type === Stmt\Use_::TYPE_NORMAL
                && in_array(strtolower($name->toString()), array('self', 'parent', 'static'))) {
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

        // Try to resolve aliases
        if (null !== $resolvedName = $this->resolveAlias($name, $type)) {
            return $resolvedName;
        }

        if ($type !== Stmt\Use_::TYPE_NORMAL && $name->isUnqualified()) {
            if (null === $this->namespace) {
                // outside of a namespace unaliased unqualified is same as fully qualified
                return new FullyQualified($name, $name->getAttributes());
            }

            // Cannot resolve statically
            return null;
        }

        // if no alias exists prepend current namespace
        return FullyQualified::concat($this->namespace, $name, $name->getAttributes());
    }

    /**
     * Get resolved class name.
     *
     * @param Name $name Class ame to resolve
     *
     * @return Name Resolved name
     */
    public function getResolvedClassName(Name $name) : Name {
        return $this->getResolvedName($name, Stmt\Use_::TYPE_NORMAL);
    }

    /**
     * Get possible ways of writing a fully qualified name (e.g., by making use of aliases)
     *
     * @param FullyQualified $name Fully-qualified name
     * @param int            $type One of Stmt\Use_::TYPE_*
     *
     * @return Name[] Possible representations of the name
     */
    public function getPossibleNames(FullyQualified $name, int $type) : array {
        $nameStr = (string) $name;
        $lcName = strtolower($name);

        // Collect possible ways to write this name, starting with the fully-qualified name
        $possibleNames = [$name];

        if (null !== $nsRelativeName = $this->getNamespaceRelativeName($name, $nameStr, $lcName)) {
            // Make sure there is no alias that makes the normally namespace-relative name
            // into something else
            if (null === $this->resolveAlias($nsRelativeName, $type)) {
                $possibleNames[] = $nsRelativeName;
            }
        }

        // Check for relevant namespace use statements
        foreach ($this->origAliases[Stmt\Use_::TYPE_NORMAL] as $alias => $orig) {
            $lcOrig = strtolower((string) $orig);
            if (0 === strpos($lcName, $lcOrig . '\\')) {
                $possibleNames[] = new Name($alias . substr($name, strlen($lcOrig)));
            }
        }

        // Check for relevant type-specific use statements
        foreach ($this->origAliases[$type] as $alias => $orig) {
            if ($type === Stmt\Use_::TYPE_CONSTANT) {
                // Constants are are complicated-sensitive
                if ($this->normalizeConstName($orig) === $this->normalizeConstName($nameStr)) {
                    $possibleNames[] = new Name($alias);
                }
            } else {
                // Everything else is case-insensitive
                if (strtolower((string) $orig) === $lcName) {
                    $possibleNames[] = new Name($alias);
                }
            }
        }

        return $possibleNames;
    }

    /**
     * Get shortest representation of this fully-qualified name.
     *
     * @param FullyQualified $name Fully-qualified name to shorten
     * @param int            $type One of Stmt\Use_::TYPE_*
     *
     * @return Name Shortest representation
     */
    public function getShortName(Name\FullyQualified $name, int $type) : Name {
        $possibleNames = $this->getPossibleNames($name, $type);

        // Find shortest name
        $shortestName = null;
        $shortestLength = INF;
        foreach ($possibleNames as $possibleName) {
            $length = strlen($possibleName->toCodeString());
            if ($length < $shortestLength) {
                $shortestName = $possibleName;
                $shortestLength = $length;
            }
        }

       return $shortestName;
    }

    private function resolveAlias(Name $name, $type) {
        $firstPart = $name->getFirst();

        if ($name->isQualified()) {
            // resolve aliases for qualified names, always against class alias table
            $checkName = strtolower($firstPart);
            if (isset($this->aliases[Stmt\Use_::TYPE_NORMAL][$checkName])) {
                $alias = $this->aliases[Stmt\Use_::TYPE_NORMAL][$checkName];
                return FullyQualified::concat($alias, $name->slice(1), $name->getAttributes());
            }
        } elseif ($name->isUnqualified()) {
            // constant aliases are case-sensitive, function aliases case-insensitive
            $checkName = $type === Stmt\Use_::TYPE_CONSTANT ? $firstPart : strtolower($firstPart);
            if (isset($this->aliases[$type][$checkName])) {
                // resolve unqualified aliases
                return new FullyQualified($this->aliases[$type][$checkName], $name->getAttributes());
            }
        }

        // No applicable aliases
        return null;
    }

    private function getNamespaceRelativeName(Name\FullyQualified $name, $nameStr, $lcName) {
        if (null === $this->namespace) {
            return new Name($name);
        }

        $namespacePrefix = strtolower($this->namespace . '\\');
        if (0 === strpos($lcName, $namespacePrefix)) {
            return new Name(substr($nameStr, strlen($namespacePrefix)));
        }

        return null;
    }

    private function normalizeConstName($name) {
        $nsSep = strrpos($name, '\\');
        if (false === $nsSep) {
            return $name;
        }

        // Constants have case-insensitive namespace and case-sensitive short-name
        $ns = substr($name, 0, $nsSep);
        $shortName = substr($name, $nsSep + 1);
        return strtolower($ns) . '\\' . $shortName;
    }
}