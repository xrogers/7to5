<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ExceptionReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\TryCatch) {
            return;
        }

        foreach ($node->catches as $catch) {
            $has_error = false;
            $needs = false;
            foreach ($catch->types as &$type) {
                if ($type instanceof Node\Name\FullyQualified &&
                    $type->getLast() === "Error") {
                    $has_error = true;
                }
                if ($type instanceof Node\Name\FullyQualified &&
                    $type->getLast() === "Throwable") {
                    $needs = true;
                    $type = new Node\Name\FullyQualified('Exception');
                }
            }
            if ($needs) {
                if (!$has_error) {
                    $catch->types[] = new Node\Name\FullyQualified('Error');
                }
            }
        }

        return $node;
    }
}
