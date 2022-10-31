<?php

namespace Hyvor\Prosemirror\Document;

use Hyvor\Prosemirror\Types\AttrsType;
use Hyvor\Prosemirror\Types\NodeType;

/**
 * @extends Node<NodeType>
 */
class TextNode extends Node
{

    public string $text;

    /**
     * @param Mark[] $marks
     */
    public function __construct(
        NodeType $type,

        AttrsType $attrs,

        string $text,

        array $marks
    )
    {
        parent::__construct($type, $attrs, new Fragment([]), $marks);
        $this->text = $text;
    }

}