<?php

class PHPParser_NodeVisitorAbstract implements PHPParser_NodeVisitorInterface
{
    public function beforeTraverse(&$node) { }
    public function enterNode(PHPParser_Node &$node) { }
    public function leaveNode(PHPParser_Node &$node) { }
    public function afterTraverse(&$node) { }
}