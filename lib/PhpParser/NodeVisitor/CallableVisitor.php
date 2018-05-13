<?php
namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * This class can be used to quickly develop some code, without creating a visitor class.
 *
 * You can set a closure for the methods the traverser calls, and will be executed when the traverser calls them.
 *
 * @method void setBeforeTraverse(callable $callable);
 * @method void setEnterNode(callable $callable);
 * @method void setLeaveNode(callable $callable);
 * @method void setAfterTraverse(callable $callable);
 */
class CallableVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $callables = [
        'beforeTraverse' => null,
        'enterNode' => null,
        'leaveNode' => null,
        'afterTraverse' => null,
    ];

    /**
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        return $this->callCallable(__FUNCTION__, $nodes);
    }

    /**
     * @param Node $node
     */
    public function enterNode(Node $node)
    {
        return $this->callCallable(__FUNCTION__, $node);
    }

    /**
     * @param Node $node
     */
    public function leaveNode(Node $node)
    {
        return $this->callCallable(__FUNCTION__, $node);
    }

    /**
     * @param array $nodes
     */
    public function afterTraverse(array $nodes)
    {
        return $this->callCallable(__FUNCTION__, $nodes);
    }

    /**
     * Call a closure for given callable name.
     *
     * @param $callableName
     * @param $argument
     */
    private function callCallable($callableName, $argument)
    {
        if (!empty($this->callables[$callableName])) {
            return $this->callables[$callableName]($argument);
        }
    }

    /**
     * Sets the callable for one of the methods the traverser calls.
     *
     * @param $method
     * @param $arguments
     * @return bool
     */
    public function __call($method, $arguments)
    {
        $name = lcfirst(str_replace('set', '', $method));

        if (!isset($arguments[0]) || !is_callable($arguments[0])) {
            return false;
        }

        if (array_key_exists($name, $this->callables)) {
            $this->callables[$name] = $arguments[0];
        }

        return true;
    }
}