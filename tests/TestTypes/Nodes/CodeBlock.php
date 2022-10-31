<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Document\Node;
use Hyvor\Prosemirror\Types\NodeType;

class CodeBlock extends NodeType
{

    public string $attrs = CodeBlockAttrs::class;

    public function toHtml(Node $node, string $children): string
    {

        $language = $node->attr('language');
        $className = $language ? "class=\"language-$language\"" : '';

        return "<pre $className><code>$children</code></pre>";

    }

}