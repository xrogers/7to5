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
        if (!($node->left instanceof Node\Expr\ErrorSuppress)) {
            $node->left = new Node\Expr\ErrorSuppress($node->left);
        }
        return new Node\Expr\FuncCall(new Node\Name('\\__coalesce'), [$test, $node->right]);
    }
}
