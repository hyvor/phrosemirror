<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Types\NodeType;

class ImageNodeType extends NodeType
{

    public string $attrs = ImageNodeAttrs::class;

    public function toHtml(Node $node, string $children): string
    {
        $src = $node->attr('src');
        $alt = $node->attr('alt');

        return "<img src=\"$src\" alt=\"$alt\" />";

    }

}