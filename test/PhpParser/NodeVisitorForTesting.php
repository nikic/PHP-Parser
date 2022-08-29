<?php declare(strict_types=1);

namespace PhpParser;

class NodeVisitorForTesting implements NodeVisitor {
    public $trace = [];
    private $returns;
    private $returnsPos;

    public function __construct(array $returns = []) {
        $this->returns = $returns;
        $this->returnsPos = 0;
    }

    public function beforeTraverse(array $nodes): ?array {
        return $this->traceEvent('beforeTraverse', $nodes);
    }

    public function enterNode(Node $node) {
        return $this->traceEvent('enterNode', $node);
    }

    public function leaveNode(Node $node) {
        return $this->traceEvent('leaveNode', $node);
    }

    public function afterTraverse(array $nodes): ?array {
        return $this->traceEvent('afterTraverse', $nodes);
    }

    private function traceEvent(string $method, $param) {
        $this->trace[] = [$method, $param];
        if ($this->returnsPos < count($this->returns)) {
            $currentReturn = $this->returns[$this->returnsPos];
            if ($currentReturn[0] === $method && $currentReturn[1] === $param) {
                $this->returnsPos++;
                return $currentReturn[2];
            }
        }
        return null;
    }

    public function __destruct() {
        if ($this->returnsPos !== count($this->returns)) {
            throw new \Exception("Expected event did not occur");
        }
    }
}
