<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\TraitUseAdaptation;

class TraitUse implements Builder
{
    protected $traits = [];
    protected $adaptations = [];

    /**
     * Creates a trait use builder.
     *
     * @param Node\Name|string ...$traits Names of used traits
     */
    public function __construct(...$traits) {
        $this->and(...$traits);
    }

    /**
     * Adds used traits.
     *
     * @param Node\Name|string ...$traits Trait names
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function and(...$traits) {
        foreach ($traits as $trait) {
            $this->traits[] = BuilderHelpers::normalizeName($trait);
        }

        return $this;
    }

    /**
     * Adds trait adaptation.
     *
     * @param TraitUseAdaptation $adaptation Trait adaptation
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function with(TraitUseAdaptation $adaptation) {
        $this->adaptations[] = $adaptation;
        return $this;
    }

    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : Node {
        return new Stmt\TraitUse($this->traits, $this->adaptations);
    }
}
