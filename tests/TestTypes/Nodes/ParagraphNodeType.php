<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class ParagraphNodeType extends NodeType
{

    public string $name = 'paragraph';

    public string $group = 'block';

    public function toHtml(Node $node, string $children): string
    {
        return "<p>$children</p>";
    }

    public function fromHtml(): array
    {
        return [
            new ParserRule(node: 'paragraph', tag: 'p'),
        ];
    }

}