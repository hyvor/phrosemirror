<?php

namespace Hyvor\Phrosemirror\Content\Expr;

class RangeExpr implements Expr
{
    public function __construct(
        public Expr $expr,
        public int $min,
        public int $max
    )
    {}
}