<?php

use PhpParser\PrettyPrinterAbstract;
use PhpParser\Node;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

class CodePrinter extends PhpParser\PrettyPrinter\Standard {

    public function pExpr_Array(PhpParser\Node\Expr\Array_ $node) {
        return '[' . $this->pCommaSeparated($node->items) . ']';
    }

    public function pStmt_ClassMethod(Stmt\ClassMethod $node) {
        return $this->pModifiers($node->type)
        . 'function ' . ($node->byRef ? '&' : '') . $node->name
        . '(' . $this->pCommaSeparated($node->params) . ')'
        . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
        . (null !== $node->stmts
            ? " " . '{' . $this->pStmts($node->stmts) . "\n" . '}' . "\n"
            : ';');
    }

    protected function pClassCommon(Stmt\Class_ $node, $afterClassToken) {
        return $this->pModifiers($node->type)
        . 'class' . $afterClassToken
        . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
        . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
        . " " . '{' . $this->pStmts($node->stmts) . "\n" . '}';
    }
}