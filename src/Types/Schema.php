<?php

namespace Hyvor\Prosemirror\Types;

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

    public function getMakrTypeByName(string $name) : ?MarkType
    {
        return $this->marks[$name] ?? null;
    }

}