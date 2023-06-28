<?php

namespace Hyvor\Phrosemirror\Types;

use Hyvor\Phrosemirror\Content\ContentExpression;
use Hyvor\Phrosemirror\Converters\HtmlParser\ParserRule;
use Hyvor\Phrosemirror\Document\Node;


abstract class NodeType
{

    public string $name;

    /**
     * @var class-string<AttrsType>
     */
    public string $attrs = AttrsType::class;

    public ?string $content = null;

    public string $group;

    public bool $inline = false;


    /**
     * @param Node $node
     * @param string $children
     * @return string
     */
    public function toHtml(Node $node, string $children) : string
    {
        return $children;
    }

    /**
     * @return ParserRule[]
     */
    public function fromHtml() : array
    {
        return [];
    }

    public function isText() : bool
    {
        return $this->name === 'text';
    }

    /**
     * @return string[]
     */
    public function getGroups() : array
    {
        if (!isset($this->group))
            return [];

        $groups = preg_split('/\s+/', $this->group);

        if (!$groups)
            return [];

        return $groups;
    }

}