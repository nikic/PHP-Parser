<?php

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Method extends PhpParser\BuilderAbstract
{
    protected $name;

    protected $type;
    protected $returnByRef;
    protected $params;
    protected $stmts;

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     */
    public function __construct($name) {
        $this->name = $name;

        $this->type = 0;
        $this->returnByRef = false;
        $this->params = array();
        $this->stmts = array();
    }

    /**
     * Makes the method public.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->setModifier(Stmt\Class_::MODIFIER_PUBLIC);

        return $this;
    }

    /**
     * Makes the method protected.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->setModifier(Stmt\Class_::MODIFIER_PROTECTED);

        return $this;
    }

    /**
     * Makes the method private.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->setModifier(Stmt\Class_::MODIFIER_PRIVATE);

        return $this;
    }

    /**
     * Makes the method static.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeStatic() {
        $this->setModifier(Stmt\Class_::MODIFIER_STATIC);

        return $this;
    }

    /**
     * Makes the method abstract.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeAbstract() {
        if (!empty($this->stmts)) {
            throw new \LogicException('Cannot make method with statements abstract');
        }

        $this->setModifier(Stmt\Class_::MODIFIER_ABSTRACT);
        $this->stmts = null; // abstract methods don't have statements

        return $this;
    }

    /**
     * Makes the method final.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeFinal() {
        $this->setModifier(Stmt\Class_::MODIFIER_FINAL);

        return $this;
    }

    /**
     * Make the method return by reference.
     *
     * @return self The builder instance (for fluid interface)
     */
    public function makeReturnByRef() {
        $this->returnByRef = true;

        return $this;
    }

    /**
     * Adds a parameter.
     *
     * @param Node\Param|Param $param The parameter to add
     *
     * @return self The builder instance (for fluid interface)
     */
    public function addParam($param) {
        $param = $this->normalizeNode($param);

        if (!$param instanceof Node\Param) {
            throw new \LogicException(sprintf('Expected parameter node, got "%s"', $param->getType()));
        }

        $this->params[] = $param;

        return $this;
    }

    /**
     * Adds multiple parameters.
     *
     * @param array $params The parameters to add
     *
     * @return self The builder instance (for fluid interface)
     */
    public function addParams(array $params) {
        foreach ($params as $param) {
            $this->addParam($param);
        }

        return $this;
    }

    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return self The builder instance (for fluid interface)
     */
    public function addStmt($stmt) {
        if (null === $this->stmts) {
            throw new \LogicException('Cannot add statements to an abstract method');
        }

        $this->stmts[] = $this->normalizeNode($stmt);

        return $this;
    }

    /**
     * Adds multiple statements.
     *
     * @param array $stmts The statements to add
     *
     * @return self The builder instance (for fluid interface)
     */
    public function addStmts(array $stmts) {
        foreach ($stmts as $stmt) {
            $this->addStmt($stmt);
        }

        return $this;
    }

    /**
     * Returns the built method node.
     *
     * @return Stmt\ClassMethod The built method node
     */
    public function getNode() {
        return new Stmt\ClassMethod($this->name, array(
            'type'   => $this->type !== 0 ? $this->type : Stmt\Class_::MODIFIER_PUBLIC,
            'byRef'  => $this->returnByRef,
            'params' => $this->params,
            'stmts'  => $this->stmts,
        ));
    }
}