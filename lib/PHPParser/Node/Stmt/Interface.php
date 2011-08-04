<?php

/**
 * @property string $name    Name
 * @property array  $extends Extended interfaces
 * @property array  $stmts   Statements
 */
class PHPParser_Node_Stmt_Interface extends PHPParser_Node_Stmt
{
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, $line, $docComment);

        if ('self' == $this->name || 'parent' == $this->name) {
            throw new PHPParser_Error(sprintf('Cannot use "%s" as class name as it is reserved', $this->name));
        }

        foreach ($this->extends as $interface) {
            if ('self' == $interface || 'parent' == $interface) {
                throw new PHPParser_Error(sprintf('Cannot use "%s" as interface name as it is reserved', $interface));
            }
        }
    }
}