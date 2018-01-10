<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Use_ extends Stmt
{
    /**
     * Unknown type. Both Stmt\Use_ / Stmt\GroupUse and Stmt\UseUse have a $type property, one of them will always be
     * TYPE_UNKNOWN while the other has one of the three other possible types. For normal use statements the type on the
     * Stmt\UseUse is unknown. It's only the other way around for mixed group use declarations.
     */
    const TYPE_UNKNOWN = 0;
    /** Class or namespace import */
    const TYPE_NORMAL = 1;
    /** Function import */
    const TYPE_FUNCTION = 2;
    /** Constant import */
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
    public function __construct(array $uses, int $type = self::TYPE_NORMAL, array $attributes = []) {
        parent::__construct($attributes);
        $this->type = $type;
        $this->uses = $uses;
    }

    public function getSubNodeNames() : array {
        return ['type', 'uses'];
    }
    
    public function getType() : string {
        return 'Stmt_Use';
    }
}
