<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/*
 * Converts define() arrays into const arrays
 */

class ArrayListReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\Assign) {
            return;
        }
        if (!$node->var instanceof Node\Expr\Array_) {
            return;
        }
        $list = true;
        foreach ($node->var->items as $item) {
            if (isset($item->key)) {
                $list = false;
                break;
            }
        }
        if ($list) {
            $node->var = new Node\Expr\List_($node->var->items);
        } else {
            $newList = [];
            $keys = [];
            $key = 0;
            foreach ($node->var->items as $item) {
                $newList []= $item->value;
                $keys []= $item->key ? $item->key : $key++;
            }
            $newList = new Node\Expr\List_($newList);
            $keys = new Node\Expr\Array_($keys);
            $node->var = $newList;
            $node->expr = new Node\Expr\FuncCall(new Node\Name('\\__destructure'), [$keys, $node->expr]);
        }
    }
}
