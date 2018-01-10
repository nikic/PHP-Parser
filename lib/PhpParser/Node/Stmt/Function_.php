<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

/**
 * @property Node\Name $namespacedName Namespaced name (if using NameResolver)
 */
class Function_ extends Node\Stmt implements FunctionLike
{
    /** @var bool Whether function returns by reference */
    public $byRef;
    /** @var Node\Identifier Name */
    public $name;
    /** @var Node\Param[] Parameters */
    public $params;
    /** @var null|Node\Identifier|Node\Name|Node\NullableType Return type */
    public $returnType;
    /** @var Node\Stmt[] Statements */
    public $stmts;

    /**
     * Constructs a function node.
     *
     * @param string|Node\Identifier $name Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'byRef'      => false  : Whether to return by reference
     *                           'params'     => array(): Parameters
     *                           'returnType' => null   : Return type
     *                           'stmts'      => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->params = $subNodes['params'] ?? [];
        $returnType = $subNodes['returnType'] ?? null;
        $this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
        $this->stmts = $subNodes['stmts'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['byRef', 'name', 'params', 'returnType', 'stmts'];
    }

    public function returnsByRef() : bool {
        return $this->byRef;
    }

    public function getParams() : array {
        return $this->params;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    /** @return Node\Stmt[] */
    public function getStmts() : array {
        return $this->stmts;
    }
    
    public function getType() : string {
        return 'Stmt_Function';
    }
}
