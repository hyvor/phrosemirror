<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class PlusExpr implements Expr
{
    public function __construct(public Expr $expr) {}
}