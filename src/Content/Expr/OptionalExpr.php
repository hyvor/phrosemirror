<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class OptionalExpr implements Expr
{
    public function __construct(public Expr $expr) {}
}