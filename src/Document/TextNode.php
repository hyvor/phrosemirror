<?php

namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Types\AttrsType;
use Hyvor\Phrosemirror\Types\NodeType;

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

    public function getSafeText() : string
    {
        return htmlspecialchars($this->text);
    }

}