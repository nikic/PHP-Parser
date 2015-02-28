<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Use_ extends Stmt
{
    const TYPE_NORMAL   = 1;
    const TYPE_FUNCTION = 2;
    const TYPE_CONSTANT = 3;

    /** @var int Type of alias */
    public $type;
    /** @var UseUse[] Aliases */
    public $uses;

    /**
     * Constructs an alias (use) list node.
     *
     * @param UseUse[] $uses       Aliases
     * @param int      $type       Type of alias
     * @param array    $attributes Additional attributes
     */
    public function __construct(array $uses, $type = self::TYPE_NORMAL, array $attributes = array()) {
        parent::__construct(null, $attributes);
        $this->type = $type;
        $this->uses = $uses;
    }

    public function getSubNodeNames() {
        return array('type', 'uses');
    }
}
