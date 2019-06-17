<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class ExceptionParamReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Param) {
            return;
        }
        $type = &$node->type;
        if (!$type instanceof Node\Name\FullyQualified ||
            $type->getLast() !== "Throwable") {
            return;
        }
        $type = null;
        return $node;
    }
}
