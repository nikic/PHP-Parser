<?php

abstract class PrettyPrinterAbstract
{
    protected $precedanceMap = array(
        'Expr_BinaryNot'        =>  1,
        'Expr_PreInc'           =>  1,
        'Expr_PreDec'           =>  1,
        'Expr_PostInc'          =>  1,
        'Expr_PostDec'          =>  1,
        'Expr_IntCast'          =>  1,
        'Expr_DoubleCast'       =>  1,
        'Expr_StringCast'       =>  1,
        'Expr_ArrayCast'        =>  1,
        'Expr_ObjectCast'       =>  1,
        'Expr_BoolCast'         =>  1,
        'Expr_UnsetCast'        =>  1,
        'Expr_ErrorSuppress'    =>  1,
        'Expr_Instanceof'       =>  2,
        'Expr_BooleanNot'       =>  3,
        'Expr_Mul'              =>  4,
        'Expr_Div'              =>  4,
        'Expr_Mod'              =>  4,
        'Expr_Plus'             =>  5,
        'Expr_Minus'            =>  5,
        'Expr_Concat'           =>  5,
        'Expr_ShiftLeft'        =>  6,
        'Expr_ShiftRight'       =>  6,
        'Expr_Smaller'          =>  7,
        'Expr_SmallerOrEqual'   =>  7,
        'Expr_Greater'          =>  7,
        'Expr_GreaterOrEqual'   =>  7,
        'Expr_Equal'            =>  8,
        'Expr_NotEqual'         =>  8,
        'Expr_Identical'        =>  8,
        'Expr_NotIdentical'     =>  8,
        'Expr_BinaryAnd'        =>  9,
        'Expr_BinaryXor'        => 10,
        'Expr_BinaryOr'         => 11,
        'Expr_BooleanAnd'       => 12,
        'Expr_BooleanOr'        => 13,
        'Expr_Ternary'          => 14,
        'Expr_Assign'           => 15,
        'Expr_AssignPlus'       => 15,
        'Expr_AssignMinus'      => 15,
        'Expr_AssignMul'        => 15,
        'Expr_AssignDiv'        => 15,
        'Expr_AssignConcat'     => 15,
        'Expr_AssignMod'        => 15,
        'Expr_AssignBinaryAnd'  => 15,
        'Expr_AssignBinaryOr'   => 15,
        'Expr_AssignBinaryXor'  => 15,
        'Expr_AssignShiftLeft'  => 15,
        'Expr_AssignShiftRight' => 15,
        'Expr_LogicalAnd'       => 16,
        'Expr_LogicalXor'       => 17,
        'Expr_LogicalOr'        => 18,
    );

    protected $precedenceStack    = array(19);
    protected $precedenceStackPos = 0;

    public function pImplode(array $nodes, $glue = '') {
        $pNodes = array();
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
        }

        return implode($glue, $pNodes);
    }

    public function pCommaSeparated(array $nodes) {
        return $this->pImplode($nodes, ', ');
    }

    public function pStmts(array $nodes) {
        $return = '';
        foreach ($nodes as $node) {
            $return .= $this->p($node);

            if ($node instanceof Node_Stmt_Func
             || $node instanceof Node_Stmt_Class
             || $node instanceof Node_Stmt_ClassMethod
             || $node instanceof Node_Stmt_Foreach
             || $node instanceof Node_Stmt_If
            ) {
                $return .= "\n";
            } else {
                $return .= ';' . "\n";
            }
        }
        return $return;
    }

    public function pIndent($string) {
        $lines = explode("\n", $string);
        foreach ($lines as &$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        }

        return implode("\n", $lines);
    }

    public function p(NodeAbstract $node) {
        $type = $node->getType();

        if (!method_exists($this, 'p' . $type)) {
            echo 'Missing: ' . 'p' . $type . "\n";

            return '';
        }

        if (isset($this->precedanceMap[$type])) {
            $precedence = $this->precedanceMap[$type];

            if ($precedence > $this->precedenceStack[$this->precedenceStackPos]) {
                $this->precedenceStack[++$this->precedenceStackPos] = $precedence;
                $return = '(' . $this->{'p' . $type}($node) . ')';
                --$this->precedenceStackPos;
            } else {
                $this->precedenceStack[++$this->precedenceStackPos] = $precedence;
                $return = $this->{'p' . $type}($node);
                --$this->precedenceStackPos;
            }

            return $return;
        } else {
            return $this->{'p' . $type}($node);
        }
    }
}