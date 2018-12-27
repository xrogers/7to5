<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;
use Spatie\Php7to5\Converter;
use Spatie\Php7to5\Exceptions\InvalidPhpCode;

class YieldReturnReplacer extends NodeVisitorAbstract
{
    protected $hasYield = [];

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            $this->hasYield []= false;
        }
        if ($node instanceof Node\Expr\Yield_ ||
            $node instanceof Node\Expr\YieldFrom
        ) {
            $this->hasYield[count($this->hasYield) - 1] = true;
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            array_pop($this->hasYield);
            return;
        }
        if (!$node instanceof Node\Stmt\Return_) {
            return;
        }
        if (!count($this->hasYield) || !$this->hasYield[count($this->hasYield) - 1]) {
            return;
        }

        $value = $node->expr;

        if (!$value) return;

        $newReturn = new Node\Expr\Yield_(
            new Node\Expr\New_(
                new Node\Expr\ConstFetch(
                    new Node\Name('\YieldReturnValue')
                ),
                [$value]
            )
        );

        return $newReturn;
    }
}
