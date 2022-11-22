<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\NodeType;

class CodeBlock extends NodeType
{

    public string $name = 'code_block';

    public string $attrs = CodeBlockAttrs::class;

    public function toHtml(Node $node, string $children): string
    {

        $language = $node->attr('language');
        $className = $language ? "class=\"language-$language\"" : '';

        return "<pre $className><code>$children</code></pre>";

    }

}