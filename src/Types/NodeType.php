<?php

namespace Hyvor\Prosemirror\Types;

use Hyvor\Prosemirror\Document\Node;


abstract class NodeType
{

    public bool $isText = false;

    /**
     * @var class-string<AttrsType>
     */
    public string $attrs = AttrsType::class;

    /**
     * @param \Hyvor\Prosemirror\Document\Node<self> $node
     * @param string $children
     * @return string
     */
    public function toHtml(Node $node, string $children) : string
    {
        return $children;
    }

    public function fromHtml() : void {}

}