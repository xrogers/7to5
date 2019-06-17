<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class YieldFromReplacer extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    protected $foreachYield;

    /*
    public function __construct()
    {
        $code = '<?php while ($___g_->valid()) {
            try {
                $___res = yield $___g_->current()
                $___g_->send($___res);
            } catch (\Throwable $___e_) {
                $___g_->throw($___e_);
            } catch (\Exception $___e_) {
                $___g_->throw($___e_);
            }
        }';
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->foreachYield = $parser->parse($code)[0];
    }*/
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\YieldFrom) {
            return;
        }

        $generator = $node->expr;
        /*
        $foreachYield = new Node\Stmt\If_(
        new Node\Expr\ConstFetch(new Node\Name('true')),
        ['stmts' => [new Node\Expr\Assign(new Node\Expr\Variable('___g_'), $generator), $this->foreachYield]]
        );*/
        $foreachYield = new Node\Expr\Yield_($generator);

        return $foreachYield;
    }
}
