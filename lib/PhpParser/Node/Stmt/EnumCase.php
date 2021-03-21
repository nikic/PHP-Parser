<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;

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
     * @param AttributeGroup[]          $attrGroups
     */
    public function __construct($name, $expr, array $attributes = [], array $attrGroups = []) {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->expr = $expr;
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['attrGroups', 'name', 'expr'];
    }

    public function getType() : string {
        return 'Stmt_EnumCase';
    }
}
