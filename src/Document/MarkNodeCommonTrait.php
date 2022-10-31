<?php

namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Html\HtmlSerializer;
use Hyvor\Phrosemirror\Types\MarkType;
use Hyvor\Phrosemirror\Types\NodeType;

trait MarkNodeCommonTrait
{

    /**
     * @param class-string<MarkType|NodeType>|class-string<MarkType|NodeType>[] $type
     */
    public function isOfType(string|array $types) : bool
    {
        $types = is_string($types) ? [$types] : $types;
        foreach ($types as $type) {
            if ($this->type instanceof $type)
                return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function attr(string $name, bool $escape = true)
    {
        return $this->attrs->get($name, $escape);
    }

    public function toHtml() : string
    {
        $serializer = new HtmlSerializer;
        return $this instanceof Node ? $serializer->node($this) : $serializer->mark($this);
    }


}