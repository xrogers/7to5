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
    protected $functions = [];
    protected $lastNodes = [];
    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        $this->lastNodes []= $node;
        if ($node instanceof Node\FunctionLike) {
            $this->functions []= $node;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        array_pop($this->lastNodes);
        foreach ($this->lastNodes as $lastNode) {
            foreach ($lastNode->getSubNodeNames() as $name) {
                if ($lastNode->$name === $node) {
                    $lastNode = $lastNode;
                    break 2;
                } elseif (is_array($lastNode->$name)) {
                    foreach ($lastNode->$name as $subNode) {
                        if ($subNode === $node) {
                            $lastNode = $lastNode->$name;
                            break 3;
                        }
                    }
                }
            }
        }

        if ($node instanceof Node\FunctionLike) {
            array_pop($this->functions);
            return;
        }
        if (!$node instanceof Node\Stmt\Return_) {
            return;
        }
        if (!count($this->functions) ||
            !$this->functions[count($this->functions) - 1] ||
            !($this->functions[count($this->functions) - 1]->hasYield ?? false)
            ) {
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

        $stmts = [$newReturn, new Node\Stmt\Return_()];
        $return = new Node\Stmt\If_(
            new Node\Expr\ConstFetch(new Node\Name('true')),
            ['stmts' => $stmts]
        );

        return is_array($lastNode) ? $stmts : $return;
    }
}
