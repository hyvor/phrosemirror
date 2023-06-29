<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class StarExpr implements Expr
{
    public function __construct(public Expr $expr) {}
}