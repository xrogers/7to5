<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

class InstanceOfReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        var_dump($node);
        if (!$node instanceof Node\Expr\Instanceof_) {
            return;
        }
        $type = $node->class;
        if (!$type instanceof Node\Name\FullyQualified ||
            $type->getLast() !== "Throwable") {
            return;
        }
        return new BooleanOr(
            new Instanceof_($node->expr, $type),
            new Instanceof_($node->expr, new FullyQualified('Error')),
        );
    }
}
