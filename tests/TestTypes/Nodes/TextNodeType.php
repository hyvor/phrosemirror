<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Types\NodeType;

class TextNodeType extends NodeType
{
    public bool $isText = true;
}