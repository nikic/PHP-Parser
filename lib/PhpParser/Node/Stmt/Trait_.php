<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike
{
    /**
     * Constructs a trait node.
     *
     * @param string|Node\Identifier $name Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'stmts'         => array(): Statements
     *                           'phpAttributes' => array(): PHP attributes
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->phpAttributes = $subNodes['phpAttributes'] ?? [];
    }

    public function getSubNodeNames() : array {
        return ['phpAttributes', 'name', 'stmts'];
    }

    public function getType() : string {
        return 'Stmt_Trait';
    }
}
