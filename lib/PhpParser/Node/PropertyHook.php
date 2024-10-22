<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class PropertyHook extends NodeAbstract implements FunctionLike {
    /** @var AttributeGroup[] PHP attribute groups */
    public array $attrGroups;
    /** @var int Modifiers */
    public int $flags;
    /** @var bool Whether hook returns by reference */
    public bool $byRef;
    /** @var Identifier Hook name */
    public Identifier $name;
    /** @var Param[] Parameters */
    public array $params;
    /** @var null|Expr|Stmt[] Hook body */
    public $body;

    /**
     * Constructs a property hook node.
     *
     * @param string|Identifier $name Hook name
     * @param null|Expr|Stmt[] $body Hook body
     * @param array{
     *     flags?: int,
     *     byRef?: bool,
     *     params?: Param[],
     *     attrGroups?: AttributeGroup[],
     * } $subNodes Array of the following optional subnodes:
     *             'byRef'      => false  : Whether hook returns by reference
     *             'params'     => array(): Parameters
     *             'attrGroups' => array(): PHP attribute groups
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct($name, $body, array $subNodes = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
        $this->body = $body;
        $this->flags = $subNodes['flags'] ?? 0;
        $this->byRef = $subNodes['byRef'] ?? false;
        $this->params = $subNodes['params'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }

    public function returnsByRef(): bool {
        return $this->byRef;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function getReturnType() {
        return null;
    }

    public function getStmts(): ?array {
        if ($this->body instanceof Expr) {
            return [new Return_($this->body)];
        }
        return $this->body;
    }

    public function getAttrGroups(): array {
        return $this->attrGroups;
    }

    public function getType(): string {
        return 'PropertyHook';
    }

    public function getSubNodeNames(): array {
        return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'body'];
    }
}
