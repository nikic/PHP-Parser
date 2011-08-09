<?php

class PHPParser_NodeVisitorAbstract implements PHPParser_NodeVisitorInterface
{
    public function beforeTraverse(&$node) { }
    public function enterNode(PHPParser_NodeAbstract &$node) { }
    public function leaveNode(PHPParser_NodeAbstract &$node) { }
    public function afterTraverse(&$node) { }
}