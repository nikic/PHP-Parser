<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Error;

/**
 * @property string                $name    Name
 * @property Node\Name[] $extends Extended interfaces
 * @property Node[]      $stmts   Statements
 */
class Interface_ extends ClassLike
{
    protected static $specialNames = array(
        'self'   => true,
        'parent' => true,
        'static' => true,
    );

    /**
     * Constructs a class node.
     *
     * @param string $name       Name
     * @param array  $subNodes   Array of the following optional subnodes:
     *                           'extends' => array(): Name of extended interfaces
     *                           'stmts'   => array(): Statements
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $subNodes = array(), array $attributes = array()) {
        parent::__construct(
            array(
                'name'    => $name,
                'extends' => isset($subNodes['extends']) ? $subNodes['extends'] : array(),
                'stmts'   => isset($subNodes['stmts'])   ? $subNodes['stmts']   : array(),
            ),
            $attributes
        );

        if (isset(self::$specialNames[(string) $this->name])) {
            throw new Error(sprintf('Cannot use \'%s\' as class name as it is reserved', $this->name));
        }

        foreach ($this->extends as $interface) {
            if (isset(self::$specialNames[(string) $interface])) {
                throw new Error(sprintf('Cannot use \'%s\' as interface name as it is reserved', $interface));
            }
        }
    }
}
