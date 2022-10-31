<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Types\NodeType;

class HeadingNodeType extends NodeType
{

    private const ALLOWED_LEVELS = [1,2,3,4,5,6];

    public string $attrs = HeadingNodeAttrs::class;

    public function toHtml(Node $node, string $children): string
    {

        $level = $node->attr('level');
        $level = in_array($level, self::ALLOWED_LEVELS) ? $level : self::ALLOWED_LEVELS[0];

        $tagName = "h$level";

        return "<$tagName>$children</$tagName>";
    }

}