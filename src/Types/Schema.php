<?php

namespace Hyvor\Phrosemirror\Types;

class Schema
{

    /** @var array<string, NodeType> */
    public array $nodes;

    /** @var array<string, MarkType> */
    public array $marks;

    /**
     * @param NodeType[] $nodes
     * @param MarkType[] $marks
     */
    public function __construct(array $nodes, array $marks)
    {

        foreach ($nodes as $node) {
            $this->nodes[$node->name] = $node;
        }

        foreach ($marks as $mark) {
            $this->marks[$mark->name] = $mark;
        }

    }

    public function getNodeTypeByName(string $name) : ?NodeType
    {
        return $this->nodes[$name] ?? null;
    }

    public function getMarkTypeByName(string $name) : ?MarkType
    {
        return $this->marks[$name] ?? null;
    }

}