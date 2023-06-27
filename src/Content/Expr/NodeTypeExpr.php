<?php

namespace Hyvor\Phrosemirror\Content\Expr;

use Hyvor\Phrosemirror\Types\NodeType;

class NodeTypeExpr implements Expr
{
    public function __construct(public NodeType $type) {}
}