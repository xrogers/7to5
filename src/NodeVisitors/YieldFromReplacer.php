<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;
use Spatie\Php7to5\Converter;
use Spatie\Php7to5\Exceptions\InvalidPhpCode;

class YieldFromReplacer extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    protected $foreachYield;

    public function __construct()
    {
        $code = '<?php while ($___g_->valid()) {
            try {
                $___g_->send(yield $___g_->current());
            } catch (\Throwable $___e_) {
                $___g_->throw($___e_);
            } catch (\Exception $___e_) {
                $___g_->throw($___e_);
            }
        }';
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->foreachYield = $parser->parse($code)[0];
    }
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\YieldFrom) {
            return;
        }

        $generator = $node->expr;

        $foreachYield = new Node\Stmt\If_(
            new Node\Expr\ConstFetch(new Node\Name('true')),
            ['stmts' => [new Node\Expr\Assign(new Node\Expr\Variable('___g_'), $generator), $this->foreachYield]]
        );

        return $foreachYield;
    }
}
