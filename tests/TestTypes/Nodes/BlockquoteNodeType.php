<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class BlockquoteNodeType extends NodeType
{

    public string $name = 'blockquote';

    public function toHtml(Node $node, string $children): string
    {
        return "<blockquote>$children</blockquote>";
    }

}