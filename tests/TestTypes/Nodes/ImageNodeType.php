<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class ImageNodeType extends NodeType
{

    public string $name = 'image';

    public string $attrs = ImageNodeAttrs::class;

    public function toHtml(Node $node, string $children): string
    {
        $src = $node->attr('src');
        $alt = $node->attr('alt');

        return "<img src=\"$src\" alt=\"$alt\" />";

    }

}