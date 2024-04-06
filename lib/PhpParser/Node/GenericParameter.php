<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class GenericParameter extends NodeAbstract
{
    /**
     * @var Identifier|Name
     */
    public NodeAbstract $name;
    /**
     * @var Identifier|Name|null
     */
    public ?NodeAbstract $constraint = null;
    /**
     * @var Identifier|Name|null
     */
    public ?NodeAbstract $default = null;

    public function __construct(NodeAbstract $name)
    {
        $this->name = $name;
        parent::__construct([]);
    }

    public function setConstraint(NodeAbstract $constraint)
    {
        $this->constraint = $constraint;
    }

    public function setDefault(NodeAbstract $default)
    {
        $this->default = $default;
    }

    public function getSubNodeNames(): array
    {
        return ['name', 'constraint', 'default'];
    }

    public function getType(): string
    {
        return 'GenericParameter';
    }
}
