<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class GenericParameter extends NodeAbstract
{
    public Name $name;
    public ?Name $constraint = null;
    public ?Name $default = null;

    public function __construct(string $name)
    {
        $this->name = new Name($name);
        parent::__construct([]);
    }

    public function setConstraint($constraint)
    {
        $this->constraint = new Name($constraint);
    }

    public function setDefault($default)
    {
        $this->default = new Name($default);
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
