<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class ChoiceExpr implements Expr
{
    /** @param Expr[] $exprs */
    public function __construct(
        public array $exprs
    ) {}
}