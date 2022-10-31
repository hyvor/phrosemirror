<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Document\Node;


abstract class NodeType
{

    public bool $isText = false;

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

}