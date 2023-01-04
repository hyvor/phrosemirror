<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Types\NodeType;

class TextNodeType extends NodeType
{

    public string $name = 'text';

    public function fromHtml(): array
    {

        return [
            new ParserRule(tag: '#text')
        ];

    }

}