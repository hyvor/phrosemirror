<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Document\Node;


abstract class NodeType
{

    public string $name;

    /**
     * @var class-string<AttrsType>
     */
    public string $attrs = AttrsType::class;

    /**
     * @param Node $node
     * @param string $children
     * @return string
     */
    public function toHtml(Node $node, string $children) : string
    {
        return $children;
    }

    public function isText() : bool
    {
        return $this->name === 'text';
    }

}