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
        return new Node\Expr\FuncCall(new Node\Name('\\__coalesce'), [$node->left, $node->right]);
    }
}
