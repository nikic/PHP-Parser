<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class EnumCase extends Node\Stmt
{
    /** @var Node\Identifier */
    public $name;
    /** @var Node\Expr|null */
    public $expr;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public $attrGroups;

    /**
     * @param string|Node\Identifier    $name
     * @param Node\Expr|null            $expr
     * @param array                     $attributes Additional attributes
     * @param array                     $subNodes   Array of the following optional subnodes:
     *                                      'attrGroups' => array() : PHP attribute groups
     */
    public function __construct($name, $expr, array $subNodes = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->expr = $expr;
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['name', 'expr', 'attrGroups'];
    }

    public function getType() : string {
        return 'Stmt_EnumCase';
    }
}
