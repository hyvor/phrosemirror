<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Types\NodeType;

class BlockquoteNodeType extends NodeType
{

    public function toHtml(Node $node, string $children): string
    {
        return "<blockquote>$children</blockquote>";
    }

}