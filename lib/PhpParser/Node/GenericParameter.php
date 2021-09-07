<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class GenericParameter extends NodeAbstract
{
    public $name;
    public $constraint;
    public $default;
    public $variance;

    const COVARIANT     = 'in';
    const CONTRAVARIANT = 'out';

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

    public function setVariance($variance)
    {
        if (!in_array($variance, [self::COVARIANT, self::CONTRAVARIANT, null])) {
            throw new \InvalidArgumentException(sprintf('Invalid generic type variance "%s"', $variance));
        }

        $this->variance = $variance;
    }

    public function getSubNodeNames(): array
    {
        return ['name', 'constraint', 'default', 'variance'];
    }

    public function getType(): string
    {
        return 'GenericParameter';
    }
}
