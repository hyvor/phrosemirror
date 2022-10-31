<?php

namespace Hyvor\Phrosemirror\Types;

class Schema
{

    public function __construct(
        /**
         * @var array<string, NodeType>
         */
        public array $nodes,
        /**
         * @var array<string, MarkType>
         */
        public array $marks
    ) {}

    public function getNodeTypeByName(string $name) : ?NodeType
    {
        return $this->nodes[$name] ?? null;
    }

    public function getMarkTypeByName(string $name) : ?MarkType
    {
        return $this->marks[$name] ?? null;
    }

}