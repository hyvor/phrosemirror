<?php

namespace Hyvor\Prosemirror\Document;

use Hyvor\Prosemirror\Html\HtmlSerializer;
use Hyvor\Prosemirror\Types\MarkType;
use Hyvor\Prosemirror\Types\NodeType;

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
    public function attr(string $name)
    {
        return $this->attrs->get($name);
    }

    public function toHtml() : string
    {
        $serializer = new HtmlSerializer;
        return $this instanceof Node ? $serializer->node($this) : $serializer->mark($this);
    }


}