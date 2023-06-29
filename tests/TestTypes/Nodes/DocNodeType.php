<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Types\NodeType;

class DocNodeType extends NodeType
{

    public string $name = 'doc';

    public ?string $content = 'block+';

}