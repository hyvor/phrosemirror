<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class SequenceExpr implements Expr
{
    /**
     * @param Expr[] $exprs
     */
    public function __construct(public array $exprs) {}
}