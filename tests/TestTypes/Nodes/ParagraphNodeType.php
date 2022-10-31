<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class ParagraphNodeType extends NodeType
{

    public function toHtml(Node $node, string $children): string
    {
        return "<p>$children</p>";
    }

}