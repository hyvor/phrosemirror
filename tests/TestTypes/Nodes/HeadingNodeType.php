<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class HeadingNodeType extends NodeType
{

    public string $name = 'heading';

    private const ALLOWED_LEVELS = [1,2,3,4,5,6];

    public string $attrs = HeadingNodeAttrs::class;

    public string $group = 'block';

    public ?string $content = 'inline*';

    public function toHtml(Node $node, string $children): string
    {

        $level = $node->attr('level');
        /** @var string $level */
        $level = in_array($level, self::ALLOWED_LEVELS) ? $level : self::ALLOWED_LEVELS[0];

        $tagName = "h$level";

        return "<$tagName>$children</$tagName>";
    }

}