<?php

namespace Hyvor\Phrosemirror\Example\Nodes;

use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Types\NodeType;

class HorizontalRule extends NodeType
{

    public string $name = 'horizontal_rule';
    public string $group = 'block';

    public function toHtml($node, $children): string
    {
        return '<hr>';
    }

    public function fromHtml(): array
    {
        return [
            new ParserRule(tag: 'hr'),
        ];
    }

}