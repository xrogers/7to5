<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\NodeVisitorAbstract;

class NullCoalesceReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Coalesce) {
            return;
        }
        $test = $node->left;
        if ($test instanceof Node\Expr\ErrorSuppress) {
            $test = $test->expr;
        }
        switch(true)
        {
            case $test instanceof Node\Expr\FuncCall:
            case $test instanceof Node\Expr\MethodCall:
            case $test instanceof Node\Expr\StaticCall:
                $notEmptyCall = new Node\Expr\BooleanNot(new Node\Expr\FuncCall(new Node\Name('empty'), [$node->left]));
                return new Node\Expr\Ternary($notEmptyCall, $node->left, $node->right);
            case $test instanceof Node\Expr\BinaryOp:
                $issetCall = new Node\Expr\FuncCall(new Node\Name('isset'), [$node->left->right]);
                $node->left->right = new Node\Expr\Ternary($issetCall, $node->left->right, $node->right);
                return $node->left;
            default:
                $issetCall = new Node\Expr\FuncCall(new Node\Name('isset'), [$node->left]);
                return new Node\Expr\Ternary($issetCall, $node->left, $node->right);
        }
    }
}
