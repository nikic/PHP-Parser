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
     * @param AttributeGroup[]          $attrGroups
     * @param array                     $attributes Additional attributes
     */
    public function __construct($name, $expr, array $attrGroups = [], array $attributes = []) {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->expr = $expr;
        $this->attrGroups = $attrGroups;
    }

    public function getSubNodeNames() : array {
        return ['attrGroups', 'name', 'expr'];
    }

    public function getType() : string {
        return 'Stmt_EnumCase';
    }
}
