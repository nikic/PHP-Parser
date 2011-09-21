<?php

abstract class PHPParser_NodeAbstract extends ArrayObject implements PHPParser_Node
{
    protected $line;
    protected $docComment;

    /**
     * Creates a Node.
     *
     * @param array       $subNodes   Array of sub nodes
     * @param int         $line       Line
     * @param null|string $docComment Nearest doc comment
     */
    public function __construct(array $subNodes, $line = -1, $docComment = null) {
        parent::__construct($subNodes, ArrayObject::ARRAY_AS_PROPS);

        $this->line       = $line;
        $this->docComment = $docComment;
    }

    /**
     * Gets the type of the node.
     *
     * @return string Type of the node
     */
    public function getType() {
        return substr(get_class($this), 15);
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Gets the nearest doc comment.
     *
     * @return null|string Nearest doc comment or null
     */
    public function getDocComment() {
        return $this->docComment;
    }
}